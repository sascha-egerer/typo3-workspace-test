<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Lina Wolf <2010@lotypo3.de>
*  based on Code of Alexander Kellner <Alexander.Kellner@einpraegsam.net>
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

/**
* Implementation of Hook from tx_comments to make the wt_spamshield work
* @author Lina Wolf <2010@lotypo3.de>
*/
class tx_wtspamshield_comments extends tslib_pibase {

	var $honeypod_inputName = 'uid987654';
	var $prefix_inputName = 'tx_comments_pi1'; 
	
	/**
	* Implementation of Hook "form" from tx_comments (when the form is rendered)
	* Adds the Honeypod input field to the marker ###JS_USER_DATA###
	* @param params array of 'pObject' => Name of extension 'markers' array of markers 'template' the template
	* @param pObj 
	* @returns the changed marker array
	*/
	function form($params, $pObj) {
		$template = $params['template'];
		$markers = $params['markers'];
		
		$this->div = t3lib_div::makeInstance('tx_wtspamshield_div'); // Generate Instance for div method
		
		if (
			!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['enable.']['comments']) &&
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
			$markers['###JS_USER_DATA###'] = $method_honeypod_instance->createHoneypod() . $markers['###JS_USER_DATA###'];	
		}
		return $markers;
	}
	
	/**
	* Implementation of Hook "externalSpamCheck" from tx_comments 
	* Test for spam and addd 1000 spampoints for each Problem found
	* @param params array of 'pObject' => Name of extension 'form' array of fields in the form 'points' excistent spam points
	* @param pObj 
	* @returns number of spam points increased by 100 for every problem that was found
	*/
	function externalSpamCheck($params, $pObj) {
		global $TSFE;
		$cObj = $TSFE->cObj; // cObject
		$error = ''; // no error at the beginning
		$form = $params['formdata'];
		$points = $params['points'];
		$this->div = t3lib_div::makeInstance('tx_wtspamshield_div'); // Generate Instance for div method
		$this->messages = $GLOBALS['TSFE']->tmpl->setup['wt_spamshield.']['message.']; // get messages from Backend
		
		if ( // only if enabled for current page
			!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['enable.']['comments']) &&
			$this->div->spamshieldIsNotDisabled()
		) {

			// 1a. blacklistCheck
			if (!$error) {
				$method_blacklist_instance = t3lib_div::makeInstance('tx_wtspamshield_method_blacklist'); // Generate Instance for session method
				$error .= $method_blacklist_instance->checkBlacklist($form, $this->messages['blacklist']);

				if (!empty($tempError)) {
					$points += 1000;
				}
				$error .= $tempError;
			}
			
			// 1b. nameCheck
			if (!$error) {
				$method_namecheck_instance = t3lib_div::makeInstance('tx_wtspamshield_method_namecheck'); // Generate Instance for namecheck method
				$tempError = $method_namecheck_instance->nameCheck($form['firstname'], $form['lastname'], $this->messages['namecheck']);
				
				if (!empty($tempError)) {
					$points += 1000;
				}
				$error .= $tempError;
			}
			
			// 1c. httpCheck
			if (!$error) {
				$method_httpcheck_instance = t3lib_div::makeInstance('tx_wtspamshield_method_httpcheck'); // Generate Instance for http method
				$tempError = $method_httpcheck_instance->httpCheck($form, $this->messages['httpcheck']);
				
				if (!empty($tempError)) {
					$points += 1000;
				}
				$error .= $tempError;
			}
			
			// 1d. sessionCheck
			if (!$error) {
				$method_session_instance = t3lib_div::makeInstance('tx_wtspamshield_method_session'); // Generate Instance for session method
				$tempError = $method_session_instance->checkSessionTime($this->messages['session.']['note1'], $this->messages['session.']['note2'], $this->messages['session.']['note3']);
				
				if (!empty($tempError)) {
					$points += 1000;
				}
				$error .= $tempError;
			}
			
			
			// 1e. honeypodCheck
			if (!$error) {
				$method_honeypod_instance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypod'); // Generate Instance for honeypod method
				$method_honeypod_instance->inputName = $this->honeypod_inputName; // name for input field
				$tempError = $method_honeypod_instance->checkHoney($form, $this->messages['honeypod']);
				
				if (!empty($tempError)) {
					$points += 1000;
				}
				$error .= $tempError;
			}
			
			
			// 1f. Akismet Check
			if (!$error) {
				$method_akismet_instance = t3lib_div::makeInstance('tx_wtspamshield_method_akismet'); // Generate Instance for Akismet method
				$tempError =  $method_akismet_instance->checkAkismet($form, $this->messages['akismet'], 'comments');
				
				if (!empty($tempError)) {
					$points += 1000;
				}
				$error .= $tempError;
			}
			
			
			// 2a. Safe log file
			if ($error) {
				$method_log_instance = t3lib_div::makeInstance('tx_wtspamshield_log'); // Generate Instance for session method
				$method_log_instance->dbLog('comments', $error, $form);
			}
			
			// 2b. Send email to admin
			if ($error) {
				$method_sendEmail_instance = t3lib_div::makeInstance('tx_wtspamshield_mail'); // Generate Instance for session method
				$method_sendEmail_instance->sendEmail('comments', $error, $form);
			}
		}
		
		return $points; 
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/ext/class.tx_wtspamshield_comments.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/ext/class.tx_wtspamshield_comments.php']);
}

?>