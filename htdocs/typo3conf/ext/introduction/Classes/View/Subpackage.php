<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Patrick Broens <patrick@patrickbroens.nl>
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
require_once(t3lib_extMgm::extPath('introduction' , 'Classes/View/Abstract.php'));

class tx_introduction_view_subpackage extends tx_introduction_view_abstract {

	/**
	 * The template file
	 *
	 * @var string
	 */
	protected $templateFile = 'typo3conf/ext/introduction/Resources/Private/Templates/Installintroduction.html';

	/**
	 * Set the subpackage for which the template should be fetched
	 *
	 * @param string $subpackage
	 * @return void
	 */
	public function setSubpackage($subpackage) {
		$this->templateFile = t3lib_extMgm::siteRelPath('introduction') . 'Resources/Private/Subpackages/' . $subpackage . '/Templates/Template.html';
	}

	public function renderProgressMessage() {
		$html = @file_get_contents(PATH_site . $this->templateFile);
		$this->template = $this->contentObject->getSubpart($html, '###PROGRESS_MESSAGE###');

		$this->applyAssignedVariables();
		return $this->template;
	}
}
?>

