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
require_once(t3lib_extMgm::extPath('wt_spamshield') . 'functions/akismet.class.php');

class tx_wtspamshield_method_akismet extends tslib_pibase {

	var $extKey = 'wt_spamshield'; // Extension key of current extension
	
	/**
	 * Function checkAkismet() send form values to akismet server and waits for the feedback if it's spam or not
	 *
	 * @param	array		$form: Array with submitted values
	 * @param	string		$note: Any existing errors
	 * @param	string		$ext: Name of extension in which the spam was recognized
	 * @return	string		$error: Return errormessage if error exists
	 */
	function checkAkismet($form, $note, $ext) {
		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]); // Get backend configuration of this extension
		$error = '';

		if (isset($conf)) { // Only if Backendconfiguration exists in localconf
			if ($conf['AkismetKey']) { // Only if enabled in backendconfiguration and key was set
				$akismet_array = array();
				
				// Get field mapping from TS
				$fields = $GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['fields.'][$ext.'.'];
				foreach ($fields as $key => $value) {
					if ($value && array_key_exists($value, $form)) {
						$akismet_array[$key] = $form[$value];
					}
				}

				$akismet_array += array(
					'user_ip' => t3lib_div::getIndpEnv('REMOTE_ADDR'),
					'user_agent' => t3lib_div::getIndpEnv('HTTP_USER_AGENT')
				);

				$akismet = new Akismet('http://' . t3lib_div::getIndpEnv('HTTP_HOST') . '/', $conf['AkismetKey'], $akismet_array); // new instance for akismet class

				if (!$akismet->isError() && $akismet->isSpam()) { // if akismet gives an error
					$error = 'Akismet detected your entry as spam entry<br />'; // default value
					
					if ($note) {
						$error = $note . '<br />'; // value from tsconfig
					}
				}
			}
		}

 		if (isset($error)) {
			return $error; // return error
		}

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/lib/class.tx_wtspamshield_method_akismet.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/lib/class.tx_wtspamshield_method_akismet.php']);
}

?>