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
require_once(t3lib_extMgm::extPath('introduction', 'Classes/Configuration/Configuration.php'));
require_once(t3lib_extMgm::extPath('introduction', 'Classes/Import/Database.php'));
require_once(t3lib_extMgm::extPath('introduction', 'Classes/Import/Filestructure.php'));
require_once(t3lib_extMgm::extPath('introduction', 'Classes/Import/Extension.php'));

class tx_introduction_controller {
	/**
	 * The view object
	 *
	 * @var tx_introduction_view_finish
	 */
	private $view;

	/**
	 * The installer Object
	 *
	 * @var tx_install
	 */
	private $installer;

	/**
	 * The configuration object which can modify the localconf.php file
	 *
	 * @var tx_introduction_configuration
	 */
	private $configuration;

	/**
	 * @var tx_introduction_import_database
	 */
	private $databaseImporter;

	/**
	 * @var tx_introduction_import_filestructure
	 */
	private $filestructureImporter;

	/**
	 * The page to request in order to test if realURL is working correctly and thus can be enabled
	 *
	 * @var string
	 */
	private $realURLTestPath = 'about-typo3/';

	/**
	 * The default color to use
	 *
	 * @var string
	 */
	private $defaultColor = '#F18F0B';

	/**
	 * The default Subpackage to install
	 *
	 * @var string
	 */
	private $defaultSubpackage = 'Introduction';

	/**
	 * Handle the incoming steps
	 *
	 * @param array $markers The markers which are used in the install tool
	 * @param string $step The step in the install process
	 * @param tx_install $callerObject The install object
	 * @return void
	 */
	public function executeStepOutput(&$markers, $step, &$callerObject) {
		$this->configuration = t3lib_div::makeInstance('tx_introduction_configuration');
		$this->databaseImporter = t3lib_div::makeInstance('tx_introduction_import_database');
		$this->filestructureImporter = t3lib_div::makeInstance('tx_introduction_import_filestructure');
		$this->installer = $callerObject;
		$this->configuration->setInstallerObject($callerObject);
		$message = '';

		switch($step) {
			case '4':
				$markers['header'] = 'Choose a package';
				$this->installPackageAction($message);
				break;
			case '5':
				if ($this->installer->INSTALL['database_import_all']) {
					$this->importDefaultTables();
				}

				if (t3lib_div::_GP('systemToInstall') == 'blank') {
					$markers['header'] = 'Congratulations,';
					$this->finishBlankAction($message);
					break;
				}

				if ((t3lib_div::_GP('systemToInstall') != '') && (t3lib_div::_GP('systemToInstall') != 'blank')) {
					$subpackageToInstall = $this->getValidSubpackage(t3lib_div::_GP('systemToInstall'));
					$this->configuration->applyDefaultConfiguration();
					$this->configuration->applySubpackageSpecificConfiguration($subpackageToInstall);
					$this->configuration->modifyLocalConfFile();
				}

				$subpackageToInstall = $this->getValidSubpackage(t3lib_div::_GP('subpackage'));
				$this->performUpdates($subpackageToInstall);
				$markers['header'] = 'Introduction package';
				$this->passwordAction($message);
				break;
			case '6':
				$markers['header'] = 'Congratulations,';
				$this->finishAction($message);
				break;
		}
		if ($message != '') {
			$markers['step'] = $message;
		}
	}

	/**
	 * Imports the default database tables which would normally be done in step 4
	 *
	 * @return void
	 */
	private function importDefaultTables() {
		$_POST['goto_step'] = $this->installer->step;
		$this->installer->action = str_replace('&step='.$this->installer->step, '&systemToInstall='.t3lib_div::_GP('systemToInstall'), $this->installer->action);
		$this->installer->checkTheDatabase();
	}

	/**
	 * Try to set NegateMask in the localconf.php, import the database structure
	 *
	 * @param string $subpackageToInstall
	 * @return void
	 */
	private function performUpdates($subpackageToInstall) {
		// As we use some GD functions to deterime the negate mask we need to check if GD is available
		if (\TYPO3\CMS\Introduction\Utility\ImageCapability::isGD()) {
			$this->configuration->modifyNegateMask();
		}

		$this->importNeededExtensions($subpackageToInstall);
		$this->databaseImporter->setSubpackage($subpackageToInstall);
		$this->databaseImporter->changeCharacterSet();
		$this->databaseImporter->importDatabase();

		$baseHref = t3lib_div::getIndpEnv('HTTP_HOST') . t3lib_div::getIndpEnv('TYPO3_SITE_PATH');
		$absrefPrefix = t3lib_div::getIndpEnv('TYPO3_SITE_PATH');

		// Remove last slash
		$baseHref = rtrim($baseHref, '/');

		// Ensure last slash
		$absrefPrefix = rtrim($absrefPrefix, '/') . '/';

		$this->filestructureImporter->setSubpackage($subpackageToInstall);
		$this->filestructureImporter->importFiles();

		// We just set the absrefPrefix value and drop the baseUrl setting:
		$this->filestructureImporter->updateBaseHref(PATH_site, '', $absrefPrefix);
	}

	/**
	 * Renders the choose a package form
	 *
	 * @param $message
	 * @return void
	 */
	public function installPackageAction(&$message) {
		require_once(t3lib_extMgm::extPath('introduction', 'Classes/View/Installintroduction.php'));
		require_once(t3lib_extMgm::extPath('introduction', 'Classes/View/Subpackage.php'));

		$subpackagesOutput = '';
		$subpackagesProgressOutput = '';
		$availableSubpackages = $this->getAvailableSubpackages();

		foreach($availableSubpackages as $subpackage) {
			/** @var $subpackageView tx_introduction_view_subpackage */
			$subpackageView = t3lib_div::makeInstance('tx_introduction_view_subpackage');
			$subpackageView->setSubpackage($subpackage);
			$subpackagesOutput .= $subpackageView->render();
			$subpackagesProgressOutput .= $subpackageView->renderProgressMessage();
		}

		$this->view = t3lib_div::makeInstance('tx_introduction_view_installintroduction');

		$directories = array(
			'fileadmin/',
			'typo3conf/',
			'typo3conf/ext/',
			'typo3temp/',
			'uploads/',
		);

		$nonWritableDirectories = '';
		foreach($directories as $directory) {
			if (!$this->configuration->isDirectoryWritable($directory)) {
				$nonWritableDirectories .= '<li>' . $directory . '</li>' . "\n";
			}
		}
		$this->view->assign('NON_WRITABLE_DIRECTORIES', $nonWritableDirectories);
		$this->view->assign('ALL_DIRECTORIES_WRITABLE', (strlen($nonWritableDirectories) ? '' : 'dummy'));

		$this->view->assign('AVAILABLE_SUBPACKAGES', $subpackagesOutput);
		$this->view->assign('AVAILABLE_SUBPACKAGES_INSTALLMESSAGES', $subpackagesProgressOutput);
		$message = $this->view->render();
	}

	/**
	 * Renders the password form
	 *
	 * @param $message
	 * @param $displayError = false Whether or not the missing password message should be displayed
	 * @return void
	 */
	public function passwordAction(&$message, $displayError = false) {
		require_once(t3lib_extMgm::extPath('introduction', 'Classes/View/Password.php'));
		$this->view = t3lib_div::makeInstance('tx_introduction_view_password');

		$this->installer->javascript[] = '<script type="text/javascript" src="' .
			t3lib_div::createVersionNumberedFilename(
				'../contrib/prototype/prototype.js'
		) . '"></script>';

		$this->view->assign('ENTER_PASSWORD' , '');
		$this->view->assign('PASSWORD', '');
		if ($displayError) {
			$this->view->assign('ENTER_PASSWORD' , 'The entered password is too short');
			$this->view->assign('PASSWORD', t3lib_div::_GP('password'));
		}

		if ($this->canModifyInputTypes()) {
			$this->view->assign('PASSWORD_TYPE' , 'password');
			$this->view->assign('PASSWORD_SWITCH_BEGIN', '');
			$this->view->assign('PASSWORD_SWITCH_END', '');
		} else {
			$this->view->assign('PASSWORD_TYPE' , 'text');
			$this->view->assign('PASSWORD_SWITCH_BEGIN', '<!--');
			$this->view->assign('PASSWORD_SWITCH_END', '-->');
		}

		$this->view->assign('CHECK_REAL_URL_COMPLIANCE_URL' , '');
		if ($this->configuration->isModRewriteEnabled()) {
			// Try to copy _.htaccess to .htaccess
			if ($this->filestructureImporter->copyHtAccessFile()) {
				$this->view->assign('CHECK_REAL_URL_COMPLIANCE_URL' , t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->realURLTestPath);
			}
		}

		if (t3lib_div::_GP('colorPicker')) {
			$this->view->assign('COLOR', t3lib_div::_GP('colorPicker'));
		} else {
			$this->view->assign('COLOR', $this->defaultColor);
		}

		$message = $this->view->render();
	}

	/**
	 * Action to perform when the blank system has been installed
	 *
	 * @param string $message The message to display
	 * @return void
	 */
	public function finishBlankAction(&$message) {
		require_once(t3lib_extMgm::extPath('introduction', 'Classes/View/Finishblank.php'));
		$this->view = t3lib_div::makeInstance('tx_introduction_view_finishblank');

		$message = $this->view->render();
	}

	/**
	 * Action when everything has been finished
	 *
	 * Render the template and show the logins for front- and backend
	 *
	 * @param string $message The message to show
	 * @return void
	 */
	public function finishAction(&$message) {
		require_once(t3lib_extMgm::extPath('introduction', 'Classes/View/Finish.php'));

		$this->clearCache();

		// Enable or disable realURL
		$this->filestructureImporter->updateRealURLConfiguration(PATH_site, t3lib_div::_GP('useRealURL'));

		$newPassword = t3lib_div::_GP('password');
		if (strlen(trim($newPassword)) < 6) {
			$this->passwordAction($message, true);
			return;
		}
		$this->configuration->modifyPasswords($newPassword);
		$this->filestructureImporter->changeColor(t3lib_div::_GP('colorPicker'));

		$this->view = t3lib_div::makeInstance('tx_introduction_view_finish');

		// Try to remove ENABLE_INSTALL_TOOL
		@unlink(PATH_typo3conf . 'ENABLE_INSTALL_TOOL');

		$this->view->assign('REMOVE_ENABLE_INSTALL_TOOL', '');
		if (is_file(PATH_typo3conf . 'ENABLE_INSTALL_TOOL')) {
			$this->view->assign('REMOVE_ENABLE_INSTALL_TOOL', 'Unfortunately it was not possible to remove the \'ENABLE_INSTALL_TOOL\' file.<br/>As this might be a security risk, please remove the file manually.');
		}
		$message = $this->view->render();
	}

	/**
	 * Import all the needed extentions and enable them
	 *
	 * @param string $subpackageToInstall
	 * @return void
	 */
	private function importNeededExtensions($subpackageToInstall) {
		/** @var $extensionImporter tx_introduction_import_extension */
		$extensionImporter = t3lib_div::makeInstance('tx_introduction_import_extension');
		$extensionImporter->setSubpackage($subpackageToInstall);

		require_once(t3lib_extMgm::extPath('introduction', 'Resources/Private/Subpackages/' . $subpackageToInstall . '/Configuration.php'));
		foreach ($GLOBALS['subpackageConfiguration']['extensionsToImport'] as $extensionKey) {
			$extensionImporter->importExtension($extensionKey);
		}

		foreach ($GLOBALS['subpackageConfiguration']['extensionsToEnable'] as $extensionKey) {
			$extensionImporter->enableExtension($extensionKey);
		}
	}

	/**
	 * Checks for all available subpackages, based on the Resources/Private/Subpackages directory
	 * All subpackages should have a Configuration.php
	 *
	 * @return array
	 */
	private function getAvailableSubpackages() {
		$availableSubpackages = array();
		$directories = t3lib_div::get_dirs(t3lib_extMgm::extPath('introduction', 'Resources/Private/Subpackages'));
		foreach($directories as $directory) {
			if (file_exists(t3lib_extMgm::extPath('introduction', 'Resources/Private/Subpackages/' . $directory . '/Configuration.php'))) {
				$availableSubpackages[] = $directory;
			}
		}
		return $availableSubpackages;
	}

	/**
	 * Checks if the given subpackage is valid. If not, return the default
	 *
	 * @param string $subpackage
	 * @return string
	 */
	private function getValidSubpackage($subpackage) {
		if (!in_array($subpackage, $this->getAvailableSubpackages())) {
			return $this->defaultSubpackage;
		}
		return $subpackage;
	}

	/**
	 * Clears the TYPO3 cache by creating a BE session
	 *
	 * @return void
	 */
	private function clearCache() {
	    	// make sure we have the cacheFilePrefix set
		if (is_null($GLOBALS['TYPO3_LOADED_EXT']['_CACHEFILE'])) {
			$GLOBALS['TYPO3_LOADED_EXT']['_CACHEFILE'] = t3lib_extMgm::getCacheFilePrefix();
		}

		/** @var $simulatedBackendUser t3lib_beUserAuth */
		$simulatedBackendUser = t3lib_div::makeInstance('t3lib_beUserAuth');
		$simulatedBackendUser->start();
		$simulatedBackendUser->setBeUserByName('admin');

		/** @var $tce t3lib_TCEmain */
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->start('', '', $simulatedBackendUser);
		$tce->clear_cacheCmd('all');
	}

	/**
	 * @return boolean
	 */
	protected function canModifyInputTypes() {
		$clientInfo = t3lib_div::clientInfo();
		return ($clientInfo['BROWSER'] !== 'msie' || $clientInfo['VERSION'] >= 9);
	}
}
?>
