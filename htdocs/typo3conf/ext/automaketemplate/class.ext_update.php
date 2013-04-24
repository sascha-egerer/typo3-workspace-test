<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 alterNET Internet BV (support@alternet.nl)
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
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
/**
 * Update script for the 'automaketemplate' extension.
 *
 * @author	alterNET Internet BV <support@alternet.nl>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 */

/**
 * Update class - called by the Extension manager
 *
 * @author	alterNET Internet BV <support@alternet.nl>
 * @package TYPO3
 * @subpackage tx_automaketemplate
 */
class ext_update {

	private $ll = 'LLL:EXT:automaketemplate/locallang.xml:updater.';

	/**
	 * Calculates if there is a potential reason for displaying an update warning
	 * @return bool
	 */
	public function access() {
		$result = FALSE;
		$selectFields = '*';
		$fromTable = 'sys_template';
		$whereClause = 'config LIKE \'%ereg_replace%\'' . t3lib_BEfunc::BEenableFields('sys_template') . t3lib_BEfunc::deleteClause('sys_template');
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($selectFields, $fromTable, $whereClause);
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
			$result = TRUE;
		}
		return $result;
	}

	public function main() {
		$out = '<h3>' . $GLOBALS['LANG']->sL($this->ll . 'heading') . '</h3>';
		$out .= '<p>' . $GLOBALS['LANG']->sL($this->ll . 'message') . '</p>';
		return $out;
	}
}
