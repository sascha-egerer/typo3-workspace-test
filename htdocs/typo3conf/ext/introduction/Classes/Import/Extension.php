<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Peter Beernink <p.beernink@drecomm.nl>
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

define('PATH_typo3conf', PATH_site . 'typo3conf/');
require_once(PATH_site . TYPO3_mainDir . 'template.php');

class tx_introduction_import_extension {

	/**
	 * The directory containing all extensions to install
	 *
	 * @var string
	 */
	private $sourceDirectory = 'Resources/Private/Extensions';

	/**
	 * Initializes the extension manager
	 */
	public function __construct() {
		// Create an instance of language. Needed in order to make the em_index work
		$GLOBALS['LANG'] = t3lib_div::makeInstance('language');
		$GLOBALS['LANG']->csConvObj = t3lib_div::makeInstance('t3lib_cs');
	}

	/**
	 * Resets the sourceDirectory based on the given subpackage
	 *
	 * @param string $subpackage
	 * @return void
	 */
	public function setSubpackage($subpackage) {
		$this->sourceDirectory = 'Resources/Private/Subpackages/' . $subpackage . '/Extensions';
	}

	/**
	 * Imports the extension from the t3x file based on the extension key and enables it
	 *
	 * @param string $extensionKey
	 * @return void
	 */
	public function importExtension($extensionKey) {
		if (t3lib_extMgm::isLoaded($extensionKey)) {
			return;
		}
		$fileContent = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl(
			t3lib_extMgm::extPath('introduction', $this->sourceDirectory . '/' . $extensionKey . '.t3x')
		);
		/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		/** @var $terUtility \TYPO3\CMS\Extensionmanager\Utility\Connection\TerUtility */
		$terUtility = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\Connection\\TerUtility');
		$extensionData = $terUtility->decodeExchangeData($fileContent);
		/** @var $fileHandlingUtility \TYPO3\CMS\Extensionmanager\Utility\FileHandlingUtility */
		$fileHandlingUtility = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\FileHandlingUtility');
		$fileHandlingUtility->unpackExtensionFromExtensionDataArray($extensionData);
	}

	/**
	 * Enables the extension
	 *
	 * @param string $extensionKey
	 * @return void
	 */
	public function enableExtension($extensionKey) {
		if (t3lib_extMgm::isLoaded($extensionKey)) {
			return;
		}
		/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		/** @var $installUtility \TYPO3\CMS\Extensionmanager\Utility\InstallUtility */
		$installUtility = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
		$installUtility->install($extensionKey);
	}
}
?>
