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

class tx_wtspamshield_method_httpcheck extends tslib_pibase {

	var $extKey = 'wt_spamshield'; // Extension key of current extension
	var $searchstring = 'http://|https://|ftp.'; // searchstring - former http://
	
	/**
	 * Function nameCheck() to disable the same first- and lastname
	 *
	 * @param	array		$array: Array with submitted values
	 * @param	string		$note: Any existing errors
	 * @return	string		$error: Return errormessage if error exists
	 */
	function httpCheck($array, $note) {
		$this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]); // Get backend configuration of this extension
		
		if (isset($this->conf) && isset($array)) { // Only if Backendconfiguration exists in localconf
			if ($this->conf['usehttpCheck'] >= 0) { // Only if enabled in backendconfiguration (disabled if -1)
				
				$no_of_errors = 0; // init $errors
				$error = sprintf('It\'s not allowed to use more than %s links within this form', $this->conf['usehttpCheck']) . '<br />'; // default note
				if ($note) {
					$error = sprintf($note, $this->conf['usehttpCheck']) . '<br />'; // note from tsconfig
				}
				
				foreach ((array) $array as $key => $value) { // One loop for every array entry
					if (!is_array($value)) { // first level
						
						$result = array(); // init $result
						preg_match_all('@' . $this->searchstring . '@', $value, $result); // give me all http:// of current string
						if (isset($result[0])) {
							$no_of_errors += count($result[0]); // add numbers of http:// to $errors
						}
						
					} else { // second level
						if (!is_array($value2)) { // second level
						
							foreach ((array) $array[$key] as $key2 => $value2 ) { // One loop for every array entry
								
								$result = array(); // init $result
								preg_match_all('@' . $this->searchstring . '@', $value2, $result); // give me all http:// of current string
								if (isset($result[0])) {
									$no_of_errors += count($result[0]); // add numbers of http:// to $errors
								}
								
							}
							
						}
					} 
				
				}
				
				if ($no_of_errors > $this->conf['usehttpCheck']) {
					return $error; // return message if more than allowed http enters
				}
				
			}
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/lib/class.tx_wtspamshield_method_httpcheck.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/lib/class.tx_wtspamshield_method_httpcheck.php']);
}

?>