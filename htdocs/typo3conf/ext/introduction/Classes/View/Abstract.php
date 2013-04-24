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
require_once(PATH_tslib . 'class.tslib_content.php');

abstract class tx_introduction_view_abstract {
	/**
	 * The content object class
	 *
	 * @var tslib_cObj
	 */
	protected $contentObject;

	/**
	 * The HTML template
	 *
	 * @var string
	 */
	protected $template;

	/**
	 * The assigned variables
	 * @var array
	 */
	protected $assignedVariables = array();

	/**
	 * The template file
	 *
	 * @var string
	 */
	protected $templateFile;

	/**
	 * Constructs this view
	 *
	 * @return void
	 */
	public function __construct() {
		$this->contentObject = t3lib_div::makeInstance('tslib_cObj');
		$this->assign(PATH_TO_RESOURCES, t3lib_div::getIndpEnv('TYPO3_SITE_PATH').t3lib_extMgm::siteRelPath('introduction').'Resources/Public/');
	}
	/**
	 * Assign a value to the template
	 *
	 * @param string $variable
	 * @param string $value
	 * @return void
	 */
	public function assign($key, $value) {
		$this->assignedVariables[$key] = $value;
	}

	/**
	 * Render the output
	 *
	 * @return string The HTML with substituted markers
	 */
	public function render() {
		$this->getTemplate();
		$this->applyAssignedVariables();
		return $this->template;
	}

	/**
	 * Apply the assigned variables to the template.
	 * Remove any subparts which are used for wrapping when var
	 * @return unknown_type
	 */
	protected function applyAssignedVariables() {
		// Check if we have any subparts to remove when empty
		foreach ($this->assignedVariables as $key => $value) {
			$subpart = $this->contentObject->getSubpart($this->template, '###'.$key.'_REMOVEWHENEMPTY###');
			if (trim($value) == '') {
				$subpart = '';
			}
			$this->template = $this->contentObject->substituteSubpart($this->template, '###'.$key.'_REMOVEWHENEMPTY###', $subpart);
		}
		$this->template = $this->contentObject->substituteMarkerArray($this->template, $this->contentObject->fillInMarkerArray(array(), $this->assignedVariables, '', FALSE, ''), '', false, true);
	}

	/**
	 * Get the template file
	 * and only take the subpart TEMPLATE
	 *
	 * @return string The HTML with markers
	 */
	protected function getTemplate() {
		$html = @file_get_contents(PATH_site . $this->templateFile);
		$this->template = $this->contentObject->getSubpart($html, '###TEMPLATE###');
	}
}
?>
