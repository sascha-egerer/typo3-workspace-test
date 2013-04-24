<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Stefan Froemken <froemken@gmail.com>
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
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_namecheck.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_httpcheck.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_session.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_akismet.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'lib/class.tx_wtspamshield_method_honeypod.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'functions/class.tx_wtspamshield_log.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'functions/class.tx_wtspamshield_mail.php');
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'functions/class.tx_wtspamshield_div.php');

class tx_wtspamshield_ke_userregister extends tslib_pibase {

	var $honeypod_inputName = 'uid987654';
	var $prefix_inputName = 'tx_keuserregister_pi1';

	/**
	 * Function is called if form is rendered (set tstamp in session)
	 *
	 * @param	array		$markerArray: Array with markers
	 * @param	object		$pObj: parent object
	 * @param	array		$errors: Array with errors
	 * @return	void
	 */
	function additionalMarkers(&$markerArray, $pObj, $errors) {
		$this->div = t3lib_div::makeInstance('tx_wtspamshield_div'); // Generate Instance for div method

		if ( // If ke_userregister form is shown AND if spamshield should be activated
			!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['enable.']['ke_userregister']) &&
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
			$pObj->templateCode = str_replace('</form>', $method_honeypod_instance->createHoneypod() . '</form>', $pObj->templateCode); // add input field
		}
	}

	/**
	 * Function processSpecialEvaluations is called from a ke_userregister hook and gives the possibility to disable the db entry of the registration
	 *
	 * @param	array		$errors: generated errors till now
	 * @param	object		$pObj: parent object
	 * @return	void
	 */
	public function processSpecialEvaluations(&$errors, &$pObj) {
		// execute this hook only if there are no other errors
		if (is_array($errors) && count($errors)) return;

		$error = ''; // no error at the beginning
		$form = t3lib_div::_GP('tx_keuserregister_pi1'); // get POST vars
		$this->div = t3lib_div::makeInstance('tx_wtspamshield_div'); // Generate Instance for div method
		$this->messages = $GLOBALS['TSFE']->tmpl->setup['wt_spamshield.']['message.']; // get messages from Backend

		if ( // only if enabled for current page
			!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['enable.']['ke_userregister']) &&
			$this->div->spamshieldIsNotDisabled()
		) {
			// 1a. nameCheck
			if (!$error) {
				$method_namecheck_instance = t3lib_div::makeInstance('tx_wtspamshield_method_namecheck'); // Generate Instance for namecheck method
				$error .= $method_namecheck_instance->nameCheck($form['first_name'], $form['last_name'], $this->messages['namecheck']);
			}

			// 1b. httpCheck
			if (!$error) {
				$method_httpcheck_instance = t3lib_div::makeInstance('tx_wtspamshield_method_httpcheck'); // Generate Instance for http method
				$error .= $method_httpcheck_instance->httpCheck($form, $this->messages['httpcheck']);
			}

			// 1c. sessionCheck
			if (!$error) {
				$method_session_instance = t3lib_div::makeInstance('tx_wtspamshield_method_session'); // Generate Instance for session method
				$error .= $method_session_instance->checkSessionTime($this->messages['session.']['note1'], $this->messages['session.']['note2'], $this->messages['session.']['note3']);
			}

			// 1d. honeypodCheck
			if (!$error) {
				$method_honeypod_instance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypod'); // Generate Instance for honeypod method
				$method_honeypod_instance->inputName = $this->honeypod_inputName; // name for input field
				$error .= $method_honeypod_instance->checkHoney($form, $this->messages['honeypod']);
			}

			// 1e. Akismet Check
			if (!$error) {
				$method_akismet_instance = t3lib_div::makeInstance('tx_wtspamshield_method_akismet'); // Generate Instance for Akismet method
				$error .= $method_akismet_instance->checkAkismet($form, $this->messages['akismet'], 'ke_userregister');
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

			// 2c. Error message
			if ($error) {
				// Workaround: create field via TS and put it in HTML template of ke_userregister
				$errors['wt_spamshield'] = $error;
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/ext/class.tx_wtspamshield_ke_userregister.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/ext/class.tx_wtspamshield_ke_userregister.php']);
}

?>