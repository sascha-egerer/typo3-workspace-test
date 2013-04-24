<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Alexander Kellner <Alexander.Kellner@einpraegsam.net>
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

class tx_wtspamshield_div extends tslib_pibase {

	var $extKey = 'wt_spamshield'; // Extension key of current extension
	
	
	/**
	 * Disable Spamshield for current page - set entry to session
	 *
	 * @param	int		$time: how long should the disabling work (in seconds)
	 * @return	void
	 */
	function disableSpamshieldForCurrentPage($time = '600') {
		$newArray = array( // write current pid to array
			'pid' . $GLOBALS['TSFE']->id => (time() + $time)
		);
		
		$varsFromSession = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->extKey . '_' . 'disableSpamshield'); // Get array from Session
		if (is_array($varsFromSession)) {
			$newArray = array_merge($varsFromSession, $newArray); // add newArray to existing session array
		}
		
		$GLOBALS['TSFE']->fe_user->setKey('ses', $this->extKey . '_' . 'disableSpamshield', $newArray); // Generate Session with array
		$GLOBALS['TSFE']->storeSessionData(); // Save session
	}
	
	
	/**
	 * Check if spamshield function is not disabled via session
	 *
	 * @return	boolean
	 */
	function spamshieldIsNotDisabled() {
		$varsFromSession = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->extKey . '_' . 'disableSpamshield'); // Get piVars from Session
		if (is_array($varsFromSession)) { // if array
			if (array_key_exists('pid' . $GLOBALS['TSFE']->id, $varsFromSession)) { // if current page is in disable array
				if ($varsFromSession['pid' . $GLOBALS['TSFE']->id] > time()) { // if time is not runned out
					return false; // disallow
				}
			}
		}
		return true; // allow
	}
	
	
	/**
	 * Check if button "update" was clicked in the extension manager after the installation
	 *
	 * @return	void
	 */
	function checkConf() {
		$config = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]); // Get backend configuration of this extension
		if (!is_array($config)) {
			echo $this->msg('Please open wt_spamshield in the Extension Manager, scroll down and click "Update"');
		}
	}
	
	
	/**
	 * Returns message with optical flair
	 *
	 * @param	string		$str: Message to show
	 * @param	int			$pos: Is this a positive message? (0,1,2)
	 * @param	boolean		$die: Process should be died now
	 * @param	boolean		$prefix: Activate or deactivate prefix "$extKey: "
	 * @param	string		$id: id to add to the message (maybe to do some javascript effects)
	 * @return	string		$string: Manipulated string
	 */
	function msg($str, $pos = 0, $die = 0, $prefix = 1, $id = '') {
		// config
		if ($prefix) $string = $this->extKey . ($pos != 1 && $pos != 2 ? ' Error' : '') . ': ';  // Add prefix
		$string .= $str; // add string
		$URLprefix = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . '/'; // URLprefix with domain
		if (t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST') . '/' != t3lib_div::getIndpEnv('TYPO3_SITE_URL')) { // if request_host is different to site_url (TYPO3 runs in a subfolder)
		    $URLprefix .= str_replace(t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST') . '/', '', t3lib_div::getIndpEnv('TYPO3_SITE_URL')); // add folder (like "subfolder/")
		} 
		
		// let's go
		switch ($pos) {
			default: // error
				$wrap = '<div class="' . $this->extKey . '_msg_error" style="background-color: #FBB19B; background-position: 4px 4px; background-image: url(' . $URLprefix . 'typo3/gfx/error.png); background-repeat: no-repeat; padding: 5px 30px; font-weight: bold; border: 1px solid #DC4C42; margin-bottom: 20px; font-family: arial, verdana; color: #444; font-size: 12px;"';
				if ($id) $wrap .= ' id="' . $id . '"'; // add css id
				$wrap .= '>';
				break;
				
			case 1: // success
				$wrap = '<div class="' . $this->extKey . '_msg_status" style="background-color: #CDEACA; background-position: 4px 4px; background-image: url(' . $URLprefix . 'typo3/gfx/ok.png); background-repeat: no-repeat; padding: 5px 30px; font-weight: bold; border: 1px solid #58B548; margin-bottom: 20px; font-family: arial, verdana; color: #444; font-size: 12px;"';
				if ($id) $wrap .= ' id="' . $id . '"'; // add css id
				$wrap .= '>';
				break;
				
			case 2: // note
				$wrap = '<div class="' . $this->extKey . '_msg_error" style="background-color: #DDEEF9; background-position: 4px 4px; background-image: url(' . $URLprefix . 'typo3/gfx/information.png); background-repeat: no-repeat; padding: 5px 30px; font-weight: bold; border: 1px solid #8AAFC4; margin-bottom: 20px; font-family: arial, verdana; color: #444; font-size: 12px;"';
				if ($id) $wrap .= ' id="' . $id . '"'; // add css id
				$wrap .= '>';
				break;
		}
		
		if (!$die) {
			 return $wrap . $string . '</div>'; // return message
		} else {
			 die ($string); // die process and write message
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/functions/class.tx_wtspamshield_div.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/functions/class.tx_wtspamshield_div.php']);
}

?>