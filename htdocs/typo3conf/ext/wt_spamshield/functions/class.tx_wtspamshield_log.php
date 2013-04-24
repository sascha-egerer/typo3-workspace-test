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

class tx_wtspamshield_log extends tslib_pibase {

	var $extKey = 'wt_spamshield'; // Extension key of current extension
	var $dbInsert = 1; // DB insert can be disabled for testing
	
	/**
	 * Function dbLog to write a log into the database if spam was recognized
	 *
	 * @param	string		$ext: Name of extension in which the spam was recognized
	 * @param	string		$error: Error Message
	 * @param	array		$formArray: Array with submitted values
	 * @return	void
	 */
	function dbLog($ext, $error, $formArray) {
		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]); // Get backend configuration of this extension
		
		if (isset($conf)) { // Only if Backendconfiguration exists in localconf
			if ($conf['pid'] == -1) { // Deactivated
				return false;
			}

			if ($conf['pid'] == -2) { // save on current page
				$conf['pid'] = $GLOBALS['TSFE']->id;
			}
			
			$db_values = array ( // DB entry for table tx_wtspamshield_log
				'pid' => intval($conf['pid']),
				'tstamp' => time(),
				'crdate' => time(),
				'form' => $ext,
				'errormsg' => str_replace(array('<br>', '<br />'), "\n", $error),
				'pageid' => $GLOBALS['TSFE']->id,
				'ip' => t3lib_div::getIndpEnv('REMOTE_ADDR'),
				'useragent' => t3lib_div::getIndpEnv('HTTP_USER_AGENT')
			);
			// Downwards compatibility
			if (t3lib_div::int_from_ver(TYPO3_version) < 4007000) {
				$db_values += array('formvalues' => t3lib_div::view_array($formArray));
			} else {
				$db_values += array('formvalues' => t3lib_utility_Debug::viewArray($formArray));
			}
			
			if ($this->dbInsert) {
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_wtspamshield_log', $db_values); // DB entry
			}
		}
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/functions/class.tx_wtspamshield_log.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/functions/class.tx_wtspamshield_log.php']);
}

?>