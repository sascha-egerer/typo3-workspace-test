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

class tx_wtspamshield_method_unique extends tslib_pibase {

	var $extKey = 'wt_spamshield'; // Extension key of current extension
	
	/**
	 * Check if the values are in more fields and return error
	 *
	 * @param	array		$sessiondata: Array with submitted values
	 * @param	string		$note: Any existing errors
	 * @return	string		$error: Return errormessage if error exists
	 */
	function main($sessiondata, $note) {
		$this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]); // Get backend configuration of this extension
		$found = 0; // no errors at the beginning
		$wholearray = array(); // clear array

		if ($this->conf['notUnique']) { // only if there are values in the backend
			$error = 'It\'s not allowed to use same values in different fields<br />'; // default
			if ($note) {
				$error = $note . '<br />'; // get message from tsconfig
			}
			
			$myFieldArray = t3lib_div::trimExplode(';', $this->conf['notUnique'], 1); // explode at ';' for field groups
			if (is_array($myFieldArray)) { // if there is an array
				foreach ($myFieldArray as $myKey => $myValue) { // one loop for every field group
					$wholearray = array(); // clear array
					$fieldarray = t3lib_div::trimExplode(',', $myValue, 1); // explode at ','
					
					if (is_array($fieldarray)) { // if there is an array
						foreach ($fieldarray as $key => $value) { // one loop for every field
							if ($sessiondata[$value]) $wholearray[] = $sessiondata[$value]; // if value exists in session, write value to an array
						}
					}
					
					if (count($wholearray) != count(array_unique($wholearray))) { // if numbers of array values not numbers if array values without double entries
						$found = 1; // found spam
					}
				}
			}
			
			
		}
		
		if ($found) {
			return $error;
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/lib/class.tx_wtspamshield_method_unique.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/lib/class.tx_wtspamshield_method_unique.php']);
}

?>