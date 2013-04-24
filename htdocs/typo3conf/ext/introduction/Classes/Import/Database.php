<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Peter Beernink <p.beernink@drecomm.nl>
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
class tx_introduction_import_database {

	/**
	 * @var t3lib_install_Sql Instance of SQL handler
	 */
	protected $sqlHandler;

	/**
	 * Location of the sql file
	 *
	 * @var string
	 */
	private $sqlLocation = 'Resources/Private/Database/introduction.sql';

	/**
	 * Initializes this object.
	 */
	public function __construct() {
		$this->sqlHandler = t3lib_div::makeInstance('t3lib_install_Sql');
	}

	/**
	 * Resets the sqlLocation based on the given subpackage
	 *
	 * @param string $subpackage
	 * @return void
	 */
	public function setSubpackage($subpackage) {
		$this->sqlLocation = 'Resources/Private/Subpackages/' . $subpackage . '/Database/introduction.sql';
	}

	/**
	 * Changes the character set and collation of the database to the given configuration
	 *
	 * @param string $characterSet Default utf8
	 * @param string $collation Default utf8_general_ci
	 * @return void
	 */
	public function changeCharacterSet($characterSet = 'utf8' , $collation = 'utf8_general_ci') {
			// DBAL should be properly configured
		if (t3lib_extMgm::isLoaded('dbal')) {
			return;
		}
		$tables = array_keys($GLOBALS['TYPO3_DB']->admin_get_tables());
		foreach ($tables as $table) {
			// Change default character set
			$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE `'.$table.'` DEFAULT CHARACTER SET '.$characterSet);
			$resource = $GLOBALS['TYPO3_DB']->admin_query('SHOW FULL FIELDS FROM `'.$table.'`');
			while ($field = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource)) {
				if (trim($field['Collation'] == '' || $field['Collation'] == $collation)) {
					continue;
				}
				if ($field['Null'] == 'YES') {
					$nullable = ' NULL ';
				} else {
					$nullable = ' NOT NULL';
				}
				if ($field['Default'] === NULL && $field['Null'] == 'YES' ) {
					$default = ' DEFAULT NULL ';
				} elseif ($field['Default'] != '') {
					$default = ' DEFAULT '.$GLOBALS['TYPO3_DB']->fullQuoteStr($field['Default'], $table);
				} else {
					$default = '';
				}
				$fieldName = $GLOBALS['TYPO3_DB']->quoteStr($field['Field'], $table);
				$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE `'.$table.'` CHANGE `'.$fieldName.'` `'.$fieldName.'` '.$field['Type'].' CHARACTER SET '.$characterSet.' COLLATE '.$collation.' '.$nullable.' '.$default);
			}
		}
	}

	/**
	 * Import the extra records into the database.
	 *
	 * @return void
	 */
	public function importDatabase() {
		if (!file_exists(t3lib_extMgm::extPath('introduction', $this->sqlLocation))) {
			return;
		}

		$definitions = array(
			t3lib_div::getUrl(t3lib_extMgm::extPath('introduction', $this->sqlLocation)),
			t3lib_cache::getDatabaseTableDefinitions(),
		);
		$fileContents = implode(
			str_repeat(chr(10), 4),
			$definitions
		);

		$statements = $this->sqlHandler->getStatementArray($fileContents, TRUE);

		list($_, $insertCount) = $this->sqlHandler->getCreateTables($statements, TRUE);

		$fieldDefinitionsFile = $this->sqlHandler->getFieldDefinitions_fileContent($fileContents);
		$fieldDefinitionsDatabase = $this->sqlHandler->getFieldDefinitions_database();
		$difference = $this->sqlHandler->getDatabaseExtra($fieldDefinitionsFile, $fieldDefinitionsDatabase);
		$updateStatements = $this->sqlHandler->getUpdateSuggestions($difference);

		$this->sqlHandler->performUpdateQueries($updateStatements['add'] , $updateStatements['add']);
		$this->sqlHandler->performUpdateQueries($updateStatements['change'] , $updateStatements['change']);
		$this->sqlHandler->performUpdateQueries($updateStatements['create_table'] , $updateStatements['create_table']);

		foreach($insertCount as $table => $count) {
			$insertStatements = $this->sqlHandler->getTableInsertStatements($statements, $table);
			foreach($insertStatements as $insertQuery) {
				$insertQuery = rtrim($insertQuery, ';');
				$GLOBALS['TYPO3_DB']->admin_query($insertQuery);
			}
		}
	}

	/**
	 * Enables or disables the realURL extension for the introduction site
	 *
	 * @param boolean $enable Whether realURL should be enabled
	 * @return void
	 * @deprecated Settings is stored in file system, see tx_introduction_import_filestructure instead
	 */
	public function updateRealURLConfiguration($enable) {
		$replacePattern = '###ENABLE_REALURL###';
		$templateRecords = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid, config', 'sys_template', 'config LIKE \'%'.$replacePattern.'%\'');
		foreach($templateRecords as $templateRecord) {
			$typoscriptSetup = $templateRecord['config'];
			$typoscriptSetup = str_replace($replacePattern, ($enable ? '1' : '0'), $typoscriptSetup);
			$updateArray = array(
				'config' => $typoscriptSetup
			);
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('sys_template', 'uid='.$templateRecord['uid'], $updateArray);
		}
	}

	/**
	 * Updates the hostname and path.
	 *
	 * @param string $hostname
	 * @return void
	 * @deprecated since 4.7 (see file typo3conf/settings/introduction.ts instead)
	 */
	public function updateBaseHref($hostname) {
		$replacePattern = '###HOSTNAME_AND_PATH###';
		$templateRecords = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid, constants, config', 'sys_template', 'constants LIKE \'%'.$replacePattern.'%\' OR config LIKE \'%'.$replacePattern.'%\'');
		foreach($templateRecords as $templateRecord) {
			$typoscriptConstants = $templateRecord['constants'];
			$typoscriptConstants = str_replace($replacePattern, $hostname, $typoscriptConstants);
			$typoscriptSetup = $templateRecord['config'];
			$typoscriptSetup = str_replace($replacePattern, $hostname, $typoscriptSetup);
			$updateArray = array(
				'constants' => $typoscriptConstants,
				'config' => $typoscriptSetup
			);
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('sys_template', 'uid='.$templateRecord['uid'], $updateArray);
		}
	}
}

?>
