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

class tx_wtspamshield_method_session extends tslib_pibase {

	var $extKey = 'wt_spamshield'; // Extension key of current extension
	
	/**
	 * Set Timestamp in session (when the form is rendered)
	 *
	 * @param boolean $forceValue Whether to force setting the timestampe in the session
	 * @return	void
	 */
	function setSessionTime($forceValue = TRUE) {
		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]); // Get backend configuration of this extension

		$timeStamp = intval($GLOBALS['TSFE']->fe_user->getKey('ses', 'wt_spamshield_form_tstamp'));
		$isOutdated = ($timeStamp + $conf['SessionEndTime'] < time());

		if ($forceValue || $isOutdated) {
			$GLOBALS['TSFE']->fe_user->setKey('ses', 'wt_spamshield_form_tstamp', time()); // write timestamp to session
			$GLOBALS['TSFE']->storeSessionData(); // store session
		}
	}
	
	/**
	 * Return Errormessage if session it runned out
	 *
	 * @param	string		$note1: Message if timestamp in session is too old (session runned out)
	 * @param	string		$note2: Message if timestamp in session is too new (too fast in submitting)
	 * @param	string		$note3: Message if timestamp in session not exists (maybe form was not rendered)
	 * @return	string		$error: Return errormessage if error exists
	 */
	function checkSessionTime($note1, $note2, $note3) {
		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]); // Get backend configuration of this extension
		
		if (isset($conf)) { // Only if Backend configuration exists in localconf
			if ($conf['useSessionCheck'] == 1) { // Only if enabled in backend configuration
				$sess_tstamp = intval($GLOBALS['TSFE']->fe_user->getKey('ses', 'wt_spamshield_form_tstamp')); // Get timestamp from session
				
				if ($sess_tstamp > 0) { // If there is a timestamp
					if ((($sess_tstamp + $conf['SessionEndTime']) < time()) && ($conf['SessionEndTime'] > 0)) { // If it's too slow
						$error = 'Sorry, but you have waited too long to send form values<br />'; // default
						
						if ($note1) {
							$error = $note1 . '<br />'; // value from tsconfig
						}
					} 
					elseif ((($sess_tstamp + $conf['SessionStartTime']) > time()) && ($conf['SessionStartTime'] > 0)) { // If it's too fast
						$error = 'Please wait some seconds before sending form<br />'; // default
						
						if ($note2) {
							$error = $note2 . '<br />'; // value from tsconfig
						}
					}
				} else {
					$error = 'No tstamp set in session<br />'; // default
					
					if ($note3) {
						$error = $note3 . '<br />'; // value from tsconfig
					}
				}
				
			}
		} else {
			$error = 'Please update your extension (' . $this->extKey . ') in the Extension Manager<br />'; // No conf, so update ext in EM
		}
			
		return $error;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/lib/class.tx_wtspamshield_method_session.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/lib/class.tx_wtspamshield_method_session.php']);
}

?>