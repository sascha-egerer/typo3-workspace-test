<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Alexander Kellner <Alexander.Kellner@einpraegsam.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_blacklist.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_namecheck.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_httpcheck.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_session.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_akismet.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_honeypod.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'functions/class.tx_wtspamshield_log.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'functions/class.tx_wtspamshield_mail.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'functions/class.tx_wtspamshield_div.php');

class tx_wtspamshield_ve_guestbook extends tslib_pibase {

	var $honeypod_inputName = 'uid987654';
	var $prefix_inputName = 'tx_veguestbook_pi1'; 
	
	/**
	 * Function is called if form is rendered (set tstamp in session)
	 *
	 * @param	array		$markerArray: Array with markers
	 * @param	array		$row: Values from database
	 * @param	array		$config: configuration
	 * @param	object		$obj: parent object
	 * @return	array		$markerArray
	 */
	function extraItemMarkerProcessor(&$markerArray, $row, $config, &$obj) {
		$this->div = t3lib_div::makeInstance('tx_wtspamshield_div'); // Generate Instance for div method
		
		if ( // If guestbookform is shown AND if spamshield should be activated
			$obj->code == 'FORM' && 
			!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['enable.']['ve_guestbook']) &&
			$this->div->spamshieldIsNotDisabled()
		) {
			// 1. check Extension Manager configuration
			$this->div->checkConf(); // Check Extension Manager configuration
			
			// 2. Session check - generate session entry
			$method_session_instance = t3lib_div::makeInstance('tx_wtspamshield_method_session'); // Generate Instance for session method
			$method_session_instance->setSessionTime(); // Start setSessionTime() Function: Set session if form is loaded
			
			// 3. Honeypod check - generate honeypot Input field
			$method_honeypod_instance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypod'); // Generate Instance for honeypod method
			$method_honeypod_instance->inputName = $this->honeypod_inputName; 
			$method_honeypod_instance->prefix_inputName = $this->prefix_inputName; 
			$obj->templateCode = str_replace('</form>', $method_honeypod_instance->createHoneypod() . '</form>', $obj->templateCode); // add input field
		}
		return $markerArray; // return markerArray to ve_guestbook (without change)
	} 
	
	/**
	 * Function preEntryInsertProcessor is called from a guestbook hook and gives the possibility to disable the db entry of the GB
	 *
	 * @param	array		$saveData: Values to save
	 * @param	object		$obj: parent object
	 * @return	array		$saveData
	 */
	function preEntryInsertProcessor($saveData, &$obj) {
		global $TSFE;
		$cObj = $TSFE->cObj; // cObject
		$error = ''; // no error at the beginning
		
		// get GPvars, downwards compatibility
		if (t3lib_div::int_from_ver(TYPO3_version) < 4006000) {
			$form = t3lib_div::GPvar('tx_veguestbook_pi1');
		} else {
			$form = t3lib_div::_GP('tx_veguestbook_pi1');
		}
		
		$this->div = t3lib_div::makeInstance('tx_wtspamshield_div'); // Generate Instance for div method
		$this->messages = $GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['message.']; // get messages from Backend
		
		if ( // only if enabled for current page
			!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['enable.']['ve_guestbook']) &&
			$this->div->spamshieldIsNotDisabled()
		) {

			// 1a. blacklistCheck
			if (!$error) {
				$method_blacklist_instance = t3lib_div::makeInstance('tx_wtspamshield_method_blacklist'); // Generate Instance for session method
				$error .= $method_blacklist_instance->checkBlacklist($form, $this->messages['blacklist']);
			}

			// 1b. nameCheck
			if (!$error) {
				$method_namecheck_instance = t3lib_div::makeInstance('tx_wtspamshield_method_namecheck'); // Generate Instance for namecheck method
				$error .= $method_namecheck_instance->nameCheck($form['firstname'], $form['surname'], $this->messages['namecheck']);
			}
			
			// 1c. httpCheck
			if (!$error) {
				$method_httpcheck_instance = t3lib_div::makeInstance('tx_wtspamshield_method_httpcheck'); // Generate Instance for http method
				$error .= $method_httpcheck_instance->httpCheck($form, $this->messages['httpcheck']);
			}
			
			// 1d. sessionCheck
			if (!$error) {
				$method_session_instance = t3lib_div::makeInstance('tx_wtspamshield_method_session'); // Generate Instance for session method
				$error .= $method_session_instance->checkSessionTime($this->messages['session.']['note1'], $this->messages['session.']['note2'], $this->messages['session.']['note3']);
			}
			
			// 1e. honeypodCheck
			if (!$error) {
				$method_honeypod_instance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypod'); // Generate Instance for honeypod method
				$method_honeypod_instance->inputName = $this->honeypod_inputName; // name for input field
				$error .= $method_honeypod_instance->checkHoney($form, $this->messages['honeypod']);
			}
			
			// 1f. Akismet Check
			if (!$error) {
				$method_akismet_instance = t3lib_div::makeInstance('tx_wtspamshield_method_akismet'); // Generate Instance for Akismet method
				$error .= $method_akismet_instance->checkAkismet($form, $this->messages['akismet'], 've_guestbook');
			}
			
			// 2a. Safe log file
			if ($error) {
				$method_log_instance = t3lib_div::makeInstance('tx_wtspamshield_log'); // Generate Instance for session method
				$method_log_instance->dbLog('ve_guestbook', $error, $form);
			}
			
			// 2b. Send email to admin
			if ($error) {
				$method_sendEmail_instance = t3lib_div::makeInstance('tx_wtspamshield_mail'); // Generate Instance for session method
				$method_sendEmail_instance->sendEmail('ve_guestbook', $error, $form);
			}
			
			// 2c. Truncate ve_guestbook temp table
			if ($error) {
				mysql_query('TRUNCATE TABLE tx_wtspamshield_veguestbooktemp'); // Truncate ve_guestbook temp table
			}
			
			// 2d. Redirect if error happens
			if (!empty($error)) { // If error
				$saveData = array('tstamp' => time()); // add timestamp
				$obj->strEntryTable = 'tx_wtspamshield_veguestbooktemp'; // change table for saving
				$obj->config['notify_mail'] = ''; // don't send a notify email
				$obj->config['feedback_mail'] = false; // don't send a feedback mail
				$obj->config['redirect_page'] = (intval($GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['redirect.']['ve_guestbook']) > 0 ? $GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['redirect.']['ve_guestbook'] : 1); // pid to redirect
				unset($obj->tt_news); // remove superfluous tt_news piVars
			}
		}
		return $saveData; // should always return something or error will happen
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/ext/class.tx_wtspamshield_ve_guestbook.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/ext/class.tx_wtspamshield_ve_guestbook.php']);
}

?>