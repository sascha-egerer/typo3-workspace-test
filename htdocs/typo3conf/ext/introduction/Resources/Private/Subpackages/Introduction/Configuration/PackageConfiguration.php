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

/**
 * Configuration which will be included to the localconf.php during the installation
 */

return array(
	'EXT/extConf/indexed_search' => 'a:17:{s:8:"pdftools";s:9:"/usr/bin/";s:8:"pdf_mode";s:2:"20";s:5:"unzip";s:9:"/usr/bin/";s:6:"catdoc";s:9:"/usr/bin/";s:6:"xlhtml";s:9:"/usr/bin/";s:7:"ppthtml";s:9:"/usr/bin/";s:5:"unrtf";s:9:"/usr/bin/";s:9:"debugMode";s:1:"0";s:18:"fullTextDataLength";s:1:"0";s:23:"disableFrontendIndexing";s:1:"0";s:6:"minAge";s:2:"24";s:6:"maxAge";s:1:"0";s:16:"maxExternalFiles";s:1:"5";s:26:"useCrawlerForExternalFiles";s:1:"0";s:11:"flagBitMask";s:3:"192";s:16:"ignoreExtensions";s:0:"";s:17:"indexExternalURLs";s:1:"0";}',
	'EXT/extConf/realurl' => 'a:4:{s:10:"configFile";s:26:"typo3conf/realurl_conf.php";s:14:"enableAutoConf";s:1:"1";s:14:"autoConfFormat";s:1:"1";s:12:"enableDevLog";s:1:"0";}',
	'EXT/extConf/news' => 'a:10:{s:29:"removeListActionFromFlexforms";s:1:"0";s:20:"pageModuleFieldsNews";s:313:"LLL:EXT:news/Resources/Private/Language/locallang_be.xml:pagemodule_simple=title,datetime;LLL:EXT:news/Resources/Private/Language/locallang_be.xml:pagemodule_advanced=title,datetime,teaser,category;LLL:EXT:news/Resources/Private/Language/locallang_be.xml:pagemodule_complex=title,datetime,teaser,category,archive;";s:24:"pageModuleFieldsCategory";s:17:"title,description";s:6:"tagPid";s:1:"1";s:13:"prependAtCopy";s:1:"1";s:19:"categoryRestriction";s:4:"none";s:22:"contentElementRelation";s:1:"0";s:13:"manualSorting";s:1:"0";s:11:"archiveDate";s:4:"date";s:12:"showImporter";s:1:"0";}',
	'EXT/extConf/wt_spamshield' => 'a:10:{s:12:"useNameCheck";s:1:"0";s:12:"usehttpCheck";s:1:"3";s:9:"notUnique";s:0:"";s:13:"honeypodCheck";s:1:"1";s:15:"useSessionCheck";s:1:"1";s:16:"SessionStartTime";s:2:"10";s:14:"SessionEndTime";s:3:"600";s:10:"AkismetKey";s:0:"";s:12:"email_notify";s:0:"";s:3:"pid";s:2:"-1";}',
	'EXT/extConf/rtehtmlarea' => 'a:13:{s:21:"noSpellCheckLanguages";s:23:"ja,km,ko,lo,th,zh,b5,gb";s:15:"AspellDirectory";s:15:"/usr/bin/aspell";s:17:"defaultDictionary";s:2:"en";s:14:"dictionaryList";s:2:"en";s:20:"defaultConfiguration";s:105:"Typical (Most commonly used features are enabled. Select this option if you are unsure which one to use.)";s:12:"enableImages";s:1:"1";s:20:"enableInlineElements";s:1:"0";s:19:"allowStyleAttribute";s:1:"1";s:24:"enableAccessibilityIcons";s:1:"0";s:16:"enableDAMBrowser";s:1:"0";s:16:"forceCommandMode";s:1:"0";s:15:"enableDebugMode";s:1:"0";s:23:"enableCompressedScripts";s:1:"1";}',
	'FE/pageNotFound_handling' => t3lib_div::getIndpEnv('TYPO3_SITE_URL').'index.php?id=16',
);
?>