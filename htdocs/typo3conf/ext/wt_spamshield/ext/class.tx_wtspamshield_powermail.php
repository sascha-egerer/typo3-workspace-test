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
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_session.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_httpcheck.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_namecheck.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_akismet.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_unique.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_honeypod.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'functions/class.tx_wtspamshield_log.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'functions/class.tx_wtspamshield_mail.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'functions/class.tx_wtspamshield_div.php');

class tx_wtspamshield_powermail extends tslib_pibase {

	var $honeypod_inputName = 'uid987654';
	var $prefix_inputName = 'tx_powermail_pi1'; 
	
	
	/**
	 * Function PM_FormWrapMarkerHook() to manipulate whole formwrap
	 *
	 * @param	array	$OuterMarkerArray: Marker Array out of the loop from powermail
	 * @param	array	$subpartArray: subpartArray Array from powermail
	 * @param	array	$conf: ts configuration from powermail
	 * @param	array	$obj: Parent Object
	 * @return	void
	 */
	function PM_FormWrapMarkerHook($OuterMarkerArray, &$subpartArray, $conf, $obj) {
		$this->div = t3lib_div::makeInstance('tx_wtspamshield_div'); // Generate Instance for div method
		
		if ( // if spamshield should be activated
			!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['enable.']['powermail']) &&
			$this->div->spamshieldIsNotDisabled()
		) {
			// 1. check Extension Manager configuration
			$this->div->checkConf(); 
			
			// 2. Set session on form create
			$method_session_instance = t3lib_div::makeInstance('tx_wtspamshield_method_session'); // Generate Instance for session method
			$method_session_instance->setSessionTime(); // Start setSessionTime() Function: Set session if form is loaded
			
			// 3. Add Honeypod
			$method_honeypod_instance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypod'); // Generate Instance for honeypod method
			$method_honeypod_instance->inputName = $this->honeypod_inputName; // name for input field
			$method_honeypod_instance->prefix_inputName = $this->prefix_inputName; // prefix
			$subpartArray['###POWERMAIL_CONTENT###'] .= $method_honeypod_instance->createHoneypod(); // Add honeypod to content
		}
		
	} 
	
	
	/**
	 * Function PM_FieldWrapMarkerHook() to manipulate Fieldwraps
	 *
	 * @param	array	$obj: Parent Object
	 * @param	array	$markerArray: Marker Array from powermail
	 * @param	array	$sessiondata: Values from powermail Session
	 * @return	string	If not false is returned, powermail will show an error. If string is returned, powermail will show this string as errormessage
	 */
	function PM_SubmitBeforeMarkerHook($obj, $markerArray = array(), $sessiondata = array()) {
		// config
		$error = ''; // no error at the beginning

		// get GPvars, downwards compatibility
		if (t3lib_div::int_from_ver(TYPO3_version) < 4006000) {
			$form = t3lib_div::GPvar('tx_powermail_pi1');
		} else {
			$form = t3lib_div::_GP('tx_powermail_pi1');
		}

		$this->messages = $GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['message.']; // Get messages from TS
		$this->div = t3lib_div::makeInstance('tx_wtspamshield_div'); // Generate Instance for div method

		if ( // if spamshield should be activated
			!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['enable.']['powermail']) &&
			$this->div->spamshieldIsNotDisabled()
		) {
			
			// 1a. blacklistCheck
			if (!$error) {
				$method_blacklist_instance = t3lib_div::makeInstance('tx_wtspamshield_method_blacklist'); // Generate Instance for session method
				$error .= $method_blacklist_instance->checkBlacklist($sessiondata, $this->messages['blacklist']);
			}

			// 1b. sessionCheck
			if (!$error) {
				$method_session_instance = t3lib_div::makeInstance('tx_wtspamshield_method_session'); // Generate Instance for session method
				$error .= $method_session_instance->checkSessionTime($this->messages['session.']['note1'], $this->messages['session.']['note2'], $this->messages['session.']['note3']);
			}
			
			// 1c. httpCheck
			if (!$error) {
				$method_httpcheck_instance = t3lib_div::makeInstance('tx_wtspamshield_method_httpcheck'); // Generate Instance for httpCheck method
				$error .= $method_httpcheck_instance->httpCheck($sessiondata, $this->messages['httpcheck']);
			}
			
			// 1d. uniqueCheck
			if (!$error) {
				$method_unique_instance = t3lib_div::makeInstance('tx_wtspamshield_method_unique'); // Generate Instance for uniqueCheck method
				$error .= $method_unique_instance->main($sessiondata, $this->messages['uniquecheck']);
			}
			
			// 1e. honeypodCheck
			if (!$error) {
				$method_honeypod_instance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypod'); // Generate Instance for honeypod method
				$method_honeypod_instance->inputName = $this->honeypod_inputName; // name for input field
				$error .= $method_honeypod_instance->checkHoney($sessiondata, $this->messages['honeypod']);
			}
			
			// 1f. Akismet Check
			if (!$error) {
				$method_akismet_instance = t3lib_div::makeInstance('tx_wtspamshield_method_akismet'); // Generate Instance for Akismet method
				$error .= $method_akismet_instance->checkAkismet($form, $this->messages['akismet'], 'powermail');
			}
			
			// 2a. Safe log file
			if ($error) {
				$method_log_instance = t3lib_div::makeInstance('tx_wtspamshield_log'); // Generate Instance for logging method
				$method_log_instance->dbLog('powermail', $error, $sessiondata);
			}
			
			// 2b. Send email to admin
			if ($error) {
				$method_sendEmail_instance = t3lib_div::makeInstance('tx_wtspamshield_mail'); // Generate Instance for email method
				$method_sendEmail_instance->sendEmail('powermail', $error, $sessiondata);
			}
			
			// 2c. Return Error message if exists
			if (!empty($error)) { // If error
				return '<div class="wtspamshield-errormsg">' . $error . '</div>';
			}
		}
		
		return false;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/ext/class.tx_wtspamshield_powermail.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/ext/class.tx_wtspamshield_powermail.php']);
}
?>