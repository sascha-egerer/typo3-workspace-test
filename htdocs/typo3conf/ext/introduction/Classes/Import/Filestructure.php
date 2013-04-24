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
class tx_introduction_import_filestructure {

	/**
	 * The directory containing all files to copy
	 *
	 * @var string
	 */
	private $sourceDirectory = 'Resources/Private/Files';

	/**
	 * The CSS files in which the color should be replaced
	 *
	 * @var array
	 */
	private $cssFiles = array(
		'fileadmin/default/templates/css/stylesheet.css',
		'fileadmin/default/templates/css/frontpage.css',
	);

	/**
	 *
	 */
	private $defaultColor = '#F18F0B';

	/**
	 * Resets the sourceDirectory based on the given subpackage
	 *
	 * @param string $subpackage
	 * @return void
	 */
	public function setSubpackage($subpackage) {
		$this->sourceDirectory = 'Resources/Private/Subpackages/' . $subpackage . '/Files';
	}

	/**
	 * Copies a directory recursive to another directory
	 *
	 * @param string $sourceDirectory
	 * @param string $destinationDirectory
	 * @return void
	 */
	private function copyRecursive($sourceDirectory, $destinationDirectory) {
		if (!is_dir($destinationDirectory)) {
			mkdir($destinationDirectory);
		}
		if (is_dir($sourceDirectory)) {
			$directoryHandle = opendir($sourceDirectory);
			while ($file = readdir($directoryHandle)) {
				if ($file != '.' && $file != '..' && $file != '.svn') {
					if (!is_dir($sourceDirectory.'/'.$file)) {
						copy($sourceDirectory.'/'.$file, $destinationDirectory.'/'.$file);
					} else {
						$this->copyRecursive($sourceDirectory.'/'.$file, $destinationDirectory.'/'.$file);
					}
				}
			}
			closedir($directoryHandle);
		}
	}

	/**
	 * Copies all files from the package to their corresponding directories
	 *
	 * @return void
	 */
	public function importFiles() {
		$this->copyRecursive(t3lib_extMgm::extPath('introduction', $this->sourceDirectory), PATH_site);
		$this->fixPermissions(t3lib_extMgm::extPath('introduction', $this->sourceDirectory), PATH_site);
	}

	/**
	 * Updates the hostname and path.
	 *
	 * @param string $destinationDirectory The directory in which the files have been copied
	 * @param string $hostname The (current) hostname to be set
	 * @param string $path The (current) path to be used as prefix
	 * @return void
	 */
	public function updateBaseHref($destinationDirectory, $hostname, $path) {
		foreach (glob($destinationDirectory . '/typo3conf/settings/*') as $filename) {
			$originalContent = $content = file_get_contents($filename);

			if (strpos($content, '###HOSTNAME###')) {
				$content = str_replace('###HOSTNAME###', $hostname, $content);
			}

			if (strpos($content, '###PATH###')) {
				$content = str_replace('###PATH###', $path, $content);
			}

			if ($originalContent !== $content) {
				file_put_contents($filename, $content);
			}
		}
	}

	/**
	 * Enables or disables the realURL extension for the introduction site
	 *
	 * @param string $destinationDirectory The directory in which the files have been copied
	 * @param boolean $enable Whether realURL should be enabled
	 * @return void
	 */
	public function updateRealURLConfiguration($destinationDirectory, $enable) {
		foreach (glob($destinationDirectory . '/typo3conf/settings/*') as $filename) {
			$originalContent = $content = file_get_contents($filename);

			if (strpos($content, '###ENABLE_REALURL###')) {
				$content = str_replace('###ENABLE_REALURL###', ($enable ? 1 : 0), $content);
			}

			if ($originalContent !== $content) {
				file_put_contents($filename, $content);
			}
		}
	}

	/**
	 * Fixes permissions of the copied files
	 *
	 * @param string $sourceDirectory The directory from which the directorystructure can be fetched
	 * @param string $destinationDirectory The directory in which the files where copied
	 * @return void
	 */
	private function fixPermissions($sourceDirectory, $destinationDirectory) {
		if (is_dir($sourceDirectory)) {
			$directoryHandle = opendir($sourceDirectory);
			while ($file = readdir($directoryHandle)) {
				if ($file != '.' && $file != '..' && $file != '.svn') {
					t3lib_div::fixPermissions($destinationDirectory . $file, TRUE);
				}
			}
			closedir($directoryHandle);
		}
	}

	/**
	 * Tries to create the .htaccess file but only if not existing already
	 *
	 * @return boolean whether or not successfully created
	 */
	public function createHtAccessFile() {
		$success = file_exists(PATH_site.'.htaccess');
		if (!$success) {
			$htaccessContent = '<IfModule mod_rewrite.c>
RewriteEngine On

# Prevent serving TYPO3 404 pages for missing files
RewriteRule ^(typo3(conf|temp)?|fileadmin|uploads|t3lib|clear.gif|index.php|favicon.ico) - [L]

# Do not rewrite static resources
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d [OR]
RewriteCond %{REQUEST_FILENAME} -l
RewriteRule .* - [L]

# Rewrite the rest to index.php
RewriteRule .* index.php [L]
</IfModule>';
			$success = t3lib_div::writeFile(PATH_site . '.htaccess', $htaccessContent);
		}
		return $success;
	}

	/**
	 * Tries to copy the _.htaccess file to .htaccess but only if not existing already
	 *
	 * @return boolean whether or not successfully copied
	 */
	public function copyHtAccessFile() {
		$success = FALSE;
		if (!file_exists(PATH_site . '.htaccess')) {
			$success = @copy(PATH_site . '_.htaccess', PATH_site . '.htaccess');
		} else {
			$success = (filesize(PATH_site.'.htaccess') > 0);
		}
		return $success;
	}

	/**
	 * Change the color in the CSS file by replacing the markers
	 *
	 * @param string $color
	 * @return void
	 */
	public function changeColor($color) {
		foreach ($this->cssFiles as $cssFile) {
			$cssContent = file_get_contents(PATH_site.$cssFile);
			$cssContent = str_replace($this->defaultColor, $color, $cssContent);
			file_put_contents(PATH_site.$cssFile, $cssContent);
		}
	}
}
?>
