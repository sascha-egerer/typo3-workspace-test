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

class tx_wtspamshield_method_honeypod extends tslib_pibase {

	var $extKey = 'wt_spamshield'; // Extension key of current extension
	var $inputName; // Name for input field
	var $prefix_inputName; // Prefix for input name
	
	/**
	 * Function createHoneypod() creates a non-visible input field
	 *
	 * @return	string		$code: Return form field (honeypod)
	 */
	function createHoneypod() {
		$this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]); // Get backend configuration of this extension
		if ($this->conf['honeypodCheck']) { // only if honeypodcheck was enabled in ext Manager
			$code = '<input type="text" name="';
			$code .= $this->prefix_inputName . '[' . $this->inputName . ']"';
			$code .= ' style="position: absolute; margin: 0 0 0 -9999px;" value=""';
			$code .= ' class="' . $this->prefix_inputName . '_tastyhoney" />';
			
			return $code;
		}
	}
	
	/**
	 * Function checkHoney() checks if a fly is in the honeypod
	 *
	 * @param	array		$sessiondata: Array with submitted values
	 * @param	string		$note: Any existing errors
	 * @return	string		$error: Return errormessage if error exists
	 */
	function checkHoney(&$sessiondata, $note = '') {
		$this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]); // Get backend configuration of this extension
		
		if (!empty($sessiondata[$this->inputName]) && $this->conf['honeypodCheck']) { // There is spam in the honeypod AND honeypodcheck was enabled in ext Manager
			return (!empty($note) ? $note : 'Entry in honeypod recognized!') . '<br />';
		}
		
		unset($sessiondata[$this->inputName]); // delete honeypot
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/lib/class.tx_wtspamshield_method_honeypod.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/lib/class.tx_wtspamshield_method_honeypod.php']);
}

?>