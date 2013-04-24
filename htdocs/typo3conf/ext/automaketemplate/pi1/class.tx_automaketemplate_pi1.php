<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2002-2004 Kasper Skårhøj (kasper@typo3.com)
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
 * Plugin 'Template Auto-parser' for the 'automaketemplate' extension.
 *
 * $Id: class.tx_automaketemplate_pi1.php 21713 2009-06-23 19:09:54Z alternet $
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @author	alterNET Internet BV <support@alternet.nl>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   56: class tx_automaketemplate_pi1 extends tslib_pibase 
 *   74:     function main($content,$conf)	
 *  174:     function recursiveBlockSplitting($content)	
 *  279:     function singleSplitting($content)	
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin class - instantiated from TypoScript. See documentation in doc/manual.sxw
 * 
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @author	alterNET Internet BV <support@alternet.nl>
 * @package TYPO3
 * @subpackage tx_automaketemplate
 */
class tx_automaketemplate_pi1 extends tslib_pibase {

		// Default extension plugin variables:
	var $prefixId = 'tx_automaketemplate_pi1'; // Same as class name
	var $scriptRelPath = 'pi1/class.tx_automaketemplate_pi1.php'; // Path to this script relative to the extension dir.
	var $extKey = 'automaketemplate'; // The extension key.

		// Others:
	/** @var t3lib_parsehtml */
	protected $htmlParse;
	protected $markersContent;
	protected $bodyTagFound;
	public $elementList;


	/**
	 * Main function, called from TypoScript
	 * 
	 * @param	string		Input content. Not used. Ignore.
	 * @param	array		TypoScript configuration of the plugin.
	 * @return	string		HTML output.
	 */
	public function main($content, $conf) {
		
			// Setting configuration internally:
		$this->conf = $conf;
		
			// Getting content:
		$content = $this->cObj->cObjGetSingle($conf['content'], $conf['content.'], 'content');

			// Making cache-hash:
		$hashConf = $conf;
		unset($hashConf['getBodyTag']);
		$hash = md5($content . '|' . serialize($hashConf));
		
			// Looking for a cached version of the parsed template:
		$hashedContent = $GLOBALS['TSFE']->sys_page->getHash($hash);
		if ($hashedContent)	{
				// Cached version found; setting values from the cache data:
			$hashedContent = unserialize($hashedContent);
			$this->markersContent = $hashedContent['markersContent'];
			$this->bodyTagFound = $hashedContent['bodyTagFound'];
			$content = $hashedContent['content'];
		} else {
			// Cached version NOT found; parsing the template
				// Initialize HTML parser object:
			$this->htmlParse = t3lib_div::makeInstance('t3lib_parsehtml');
			
				// Block elements (eg. TABLE, TD, P, DIV)
			$elements = array();
			if (is_array($this->conf['elements.']))	{
			
					// Finding all elements configured:
				foreach($this->conf['elements.'] as $elementName => $elementConfiguration) {
					if (is_array($elementConfiguration)) {
						$elements[] = rtrim($elementName, '.');
					} else {
						$elements[] = $elementName;
					}
				}
				
					// Splitting/Processing the HTML source by these tags:
				$elements = array_unique($elements);
				if (count($elements)) {
					$this->elementList = implode(',', $elements);
					$content = $this->recursiveBlockSplitting($content);
				}
			}

				// Single elements (eg. IMG, INPUT)
			$elements = array();
			if (is_array($this->conf['single.'])) {
			
					// Finding all elements configured:
				foreach($this->conf['single.'] as $elementName => $elementConfiguration) {
					if (is_array($elementConfiguration)) {
						$elements[] = rtrim($elementName, '.');
					} else {
						$elements[] = $elementName;
					}
				}
				
					// Splitting/Processing the HTML source by these tags:
				$elements = array_unique($elements);
				if (count($elements)) {
					$this->elementList = implode(',', $elements);
					$content = $this->singleSplitting($content);
				}
			}

				// Fixing all relative paths found:
			if ($this->conf['relPathPrefix']) {
				$content = $this->htmlParse->prefixResourcePath($this->conf['relPathPrefix'], $content, $this->conf['relPathPrefix.']);
			}
			
				// Finding the bodyTag of the HTML source:
			list(,$this->bodyTagFound) = $this->htmlParse->splitTags('body', $content);
			
				// Finally, save the results in the hash table:
			$GLOBALS['TSFE']->sys_page->storeHash($hash,serialize(
				array(
					'content' => $content,
					'markersContent' => $this->markersContent,
					'bodyTagFound' => $this->bodyTagFound
				)
			),'automaketemplate');
		}	

			// If the property "getBodyTag" was set, return the bodytag. Else return the processed content:
		if ($this->conf['getBodyTag']) {
			$result = $this->bodyTagFound ? $this->bodyTagFound : '';
		} else {
			$result = $content;
		}
		return $result;
	}

	/**
	 * Processing HTML content based on element list (block tags!)
	 * 
	 * @param	string		HTML content to split.
	 * @return	string		Processed HTML content
	 */
	protected function recursiveBlockSplitting($content) {
	
			// Split HTML source:
		$parts = $this->htmlParse->splitIntoBlock($this->elementList, $content, 0);
		
			// Traverse the parts:
		foreach($parts as $blockNumber => $blockContent) {
			if ($blockNumber % 2) {
			
					// Initializing:
				$firstTag = $this->htmlParse->getFirstTag($blockContent); // The first tag's content
				$firstTagName = $this->htmlParse->getFirstTagName($blockContent); // The 'name' of the first tag
				$endTag = '</' . strtolower($firstTagName) . '>';
				$blockContent = $this->htmlParse->removeFirstAndLastTag($blockContent); // Finally remove the first tag (unless we do this, the recursivity will be eternal!

					// Remove tags from source:
				if ($this->conf['elements.'][$firstTagName . '.']['rmTagSections']) {
					$elementList = t3lib_div::trimExplode(',', $this->conf['elements.'][$firstTagName.'.']['rmTagSections'], TRUE);
					$removeParts = $this->htmlParse->splitIntoBlock(implode(',', $elementList), $blockContent, 1);
					$outerParts = $this->htmlParse->getAllParts($removeParts, 0);
					$blockContent = implode('', $outerParts);
				}
				if ($this->conf['elements.'][$firstTagName . '.']['rmSingleTags']) {
					$elementList = t3lib_div::trimExplode(',', $this->conf['elements.'][$firstTagName . '.']['rmSingleTags'], TRUE);
					$removeParts = $this->htmlParse->splitTags(implode(',', $elementList), $blockContent);
					$outerParts = $this->htmlParse->getAllParts($removeParts, 0);
					$blockContent = implode('', $outerParts);
				}

					// Perform str-replace on the source:
				if (is_array($this->conf['elements.'][$firstTagName . '.']['str_replace.'])) {
					foreach($this->conf['elements.'][$firstTagName . '.']['str_replace.'] as $key => $replaceConfiguration) {
						if (is_array($replaceConfiguration) && strcmp($replaceConfiguration['value'], '')) {
							switch((string)$replaceConfiguration['useRPFunc']) {
								case 'ereg_replace':
									$blockContent = ereg_replace(
										$replaceConfiguration['value'],
										$replaceConfiguration['replaceWith'],
										$blockContent);
									t3lib_div::deprecationLog('Use of the value \'ereg_replace\' in the property useRPFunc is deprecated. Support for this will be removed when TYPO3 4.6 is released.');
									break;
								case 'preg_replace':
							        $blockContent = preg_replace(
										$replaceConfiguration['value'],
										$replaceConfiguration['replaceWith'],
										$blockContent);
							        break;
								default:
									$blockContent = str_replace(
										$replaceConfiguration['value'],
										$replaceConfiguration['replaceWith'],
										$blockContent);
									break;
							}
						}
					}
				}

					// Make the call again - recursively:
				$blockContent = $this->recursiveBlockSplitting($blockContent);

					// Check if we are going to do processing:
				$params = $this->htmlParse->get_tag_attributes($firstTag, 1);


					// Get configuration for this tag:
				$allCheck = $this->conf['elements.'][$firstTagName . '.']['all'];
				$classCheck = $params[0]['class'] && $this->conf['elements.'][$firstTagName . '.']['class.'][$params[0]['class']];
				$idCheck = $params[0]['id'] && $this->conf['elements.'][$firstTagName . '.']['id.'][$params[0]['id']];

					// If any configuration was found, do processing:
				if ($classCheck || $idCheck || $allCheck) {
					if ($allCheck) {
						$tagConfiguration = $this->conf['elements.'][$firstTagName . '.']['all.'];
					}
					if ($classCheck) {
						$tagConfiguration = $this->conf['elements.'][$firstTagName . '.']['class.'][$params[0]['class'] . '.'];
					}
					if ($idCheck) {
						$tagConfiguration = $this->conf['elements.'][$firstTagName . '.']['id.'][$params[0]['id'] . '.'];
					}

					// Create markers to insert:
					$marker = $tagConfiguration['subpartMarker']
							? $tagConfiguration['subpartMarker']
							: (
								($idCheck || $allCheck) && $params[0]['id']
										? $params[0]['id']
										: $params[0]['class']
							);
					$markerList = array(
						'<!--###' . $marker . '### begin -->',
						'<!--###' . $marker . '### end -->'
					);

						// 10359, 1648 markers without content or content which evaluates to false
						// are ignored by various routines in the core which handle the template markers
						// putting an HTML comment will fix this
					if (!$blockContent) {
						$blockContent .= '<!-- -->';
					}
						// Wrap markers...:
					if (!trim($marker)) {
							// No marker, no wrapping:
						$blockContent = $firstTag . $blockContent . $endTag;
					} elseif ($tagConfiguration['doubleWrap']) {
							// Double wrapping, both inside and outside:
						$this->markersContent[$marker][] = $blockContent;
						$blockContent = $firstTag . $markerList[0] . $blockContent . $markerList[1] . $endTag;

						$marker .= '_PRE';
						$markerList = array('<!--###' . $marker . '### begin -->', '<!--###' . $marker . '### end -->');
						$this->markersContent[$marker][] = $firstTag . $blockContent . $endTag;
						$blockContent = $markerList[0] . $blockContent . $markerList[1];
					} elseif ($tagConfiguration['includeWrappingTag']) {
							// Wrapping outside the active tag:
						$this->markersContent[$marker][] = $firstTag . $blockContent . $endTag;
						$blockContent = $markerList[0] . $firstTag . $blockContent . $endTag . $markerList[1];
					} else {
							// Default; wrapping inside the active tag:
						$this->markersContent[$marker][] = $blockContent;
						$blockContent = $firstTag . $markerList[0] . $blockContent . $markerList[1] . $endTag;
					}
				} else {
						// No config, no wrapping:
					$blockContent = $firstTag . $blockContent . $endTag;
				}
			}

				// Override the original value with the processed one:
			$parts[$blockNumber] = $blockContent;
		}

			// Implode it all back to a string and return
		return implode('', $parts);
	}

	/**
	 * Processing HTML content based on element list (single tags!)
	 * 
	 * @param	string		HTML content to split.
	 * @return	string		Processed HTML content
	 */
	protected function singleSplitting($content) {

			// Split HTML source:
		$parts = $this->htmlParse->splitTags($this->elementList, $content);

			// Traverse the parts:
		foreach ($parts as $blockNumber => $blockContent) {
			if ($blockNumber % 2) {

					// Initializing:
				$firstTag = $blockContent; // The first tag's content
				$firstTagName = $this->htmlParse->getFirstTagName($blockContent); // The 'name' of the first tag

					// Check if we are going to do processing:
				$parameters = $this->htmlParse->get_tag_attributes($firstTag, 1);

// ******** THIS IS similar to the code in recursiveBlockSplitting(), but 'elements.' substituted with 'single.': (begin)
				$allCheck = $this->conf['single.'][$firstTagName . '.']['all'];
				$classCheck = $parameters[0]['class'] && $this->conf['single.'][$firstTagName . '.']['class.'][$parameters[0]['class']];
				$idCheck = $parameters[0]['id'] && $this->conf['single.'][$firstTagName . '.']['id.'][$parameters[0]['id']];

					// If any configuration was found, do processing:
				if ($classCheck || $idCheck || $allCheck) {
					if ($allCheck) {
						$tagConfiguration = $this->conf['single.'][$firstTagName . '.']['all.'];
					}
					if ($classCheck) {
						$tagConfiguration = $this->conf['single.'][$firstTagName . '.']['class.'][$parameters[0]['class'] . '.'];
					}
					if ($idCheck) {
						$tagConfiguration = $this->conf['single.'][$firstTagName . '.']['id.'][$parameters[0]['id'] . '.'];
					}

						// Create markers to insert:
					$marker = $tagConfiguration['subpartMarker']
							? $tagConfiguration['subpartMarker']
							: (
								($idCheck || $allCheck) && $parameters[0]['id']
										? $parameters[0]['id']
										: $parameters[0]['class']
							);
					$markerList = array(
						'<!--###' . $marker . '### begin -->',
						'<!--###' . $marker . '### end -->'
					);
// ******** THIS IS similar to the code in recursiveBlockSplitting() (end)

						// If a marker was defined, wrap the tag:
					if (trim($marker)) {
						$this->markersContent[$marker][] = $blockContent;
						$blockContent = $markerList[0] . $blockContent . $markerList[1];
					}
				}
			}

				// Override the original value with the processed one:
			$parts[$blockNumber] = $blockContent;
		}

			// Implode it all back to a string and return
		return implode('', $parts);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/automaketemplate/pi1/class.tx_automaketemplate_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/automaketemplate/pi1/class.tx_automaketemplate_pi1.php']);
}
?>