<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Alex Kellner <Alexander.Kellner@in2code.de>
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

class tx_wtspamshield_method_blacklist extends tslib_pibase {

	public $extKey = 'wt_spamshield'; // Extension key of current extension
	
	/**
	 * Function checkBlacklist() checks if IP or sender is blacklisted
	 *
	 * @var		array		Form values
	 * @var		string		Error Message for Frontend Output
	 * @return	string		Return Error if blacklisted
	 */
	public function checkBlacklist($formValues, $note) {
		if ($this->isCurrentIPBlacklisted() || $this->isCurrentEmailBlacklisted($formValues)) {
			return (!empty($note) ? $note : 'Sorry, you are blacklisted!') . '<br />';
		}
	}

	/**
	 * Function isCurrentIPBlacklisted() checks if current IP is blacklisted
	 *
	 * @return	boolean
	 */
	private function isCurrentIPBlacklisted() {// Give me all fields in current fieldset, which are related to current content
		$select = 'tx_wtspamshield_blacklist.uid';
		$from = 'tx_wtspamshield_blacklist';
		$where = 'tx_wtspamshield_blacklist.value = "' . t3lib_div::getIndpEnv('REMOTE_ADDR') . '"';
		$where .= ' AND tx_wtspamshield_blacklist.type = "ip"';
		$where .= ' AND tx_wtspamshield_blacklist.whitelist = 0';
		$where .= ' AND tx_wtspamshield_blacklist.deleted = 0';
		$groupBy = '';
		$orderBy = '';
		$limit = 1;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

		if ($res !== false) { // If there is a result
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if ($row['uid'] > 0) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Function isCurrentEmailBlacklisted() checks if current Email is blacklisted
	 *
	 * @var		array	Form values
	 * @return	boolean
	 */
	private function isCurrentEmailBlacklisted($formValues) {
		$emails = array();
		$pattern = "/(?:[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/";

		// find emails in given fields
		foreach ((array) $formValues as $value) {
			preg_match_all($pattern, $value, $matches); // find emails in given string

			if (is_array($matches[0])) { // if emails found
				foreach ($matches[0] as $email) { // one loop for every found email
					$emails[] = $email;
				}
			}
		}

		// check in database
		if (count($emails) == 0) {
			return false;
		}
		foreach ($emails as $email) { // one query for every given email
			$select = 'tx_wtspamshield_blacklist.uid';
			$from = 'tx_wtspamshield_blacklist';
			$where = 'tx_wtspamshield_blacklist.value = "' . $email . '"';
			$where .= ' AND tx_wtspamshield_blacklist.type = "email"';
			$where .= ' AND tx_wtspamshield_blacklist.whitelist = 0';
			$where .= ' AND tx_wtspamshield_blacklist.deleted = 0';
			$groupBy = '';
			$orderBy = '';
			$limit = 1;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

			if ($res !== false) { // If there is a result
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				if ($row['uid'] > 0) {
					return true;
				}
			}
		}

		return false;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/lib/class.tx_wtspamshield_method_blacklist.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/lib/class.tx_wtspamshield_method_blacklist.php']);
}

?>