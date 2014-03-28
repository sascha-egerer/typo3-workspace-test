<?php
return array(
	'BE' => array(
		'debug' => TRUE,
		'disable_exec_function' => 0,
		'explicitADmode' => 'explicitAllow',
		'fileCreateMask' => '0660',
		'folderCreateMask' => '0770',
		'installToolPassword' => '$1$Uc7pF7sn$iRNw.kblt6omHL1QR33wJ1',
		'lockHashKeyWords' => '',
		'loginSecurityLevel' => 'rsa',
		'versionNumberInFilename' => '0',
	),
	'DB' => array(
		'database' => 'typo3-workspace-test-environment',
		'extTablesDefinitionScript' => 'extTables.php',
		'host' => 'localhost',
		'password' => 'iloverandompasswordsbutthiswilldo',
		'socket' => '',
		'username' => 'root',
	),
	'EXT' => array(
		'extConf' => array(
			'automaketemplate' => 'a:0:{}',
			'dkd_redis_sessions' => 'a:4:{s:15:"frontend_server";s:14:"localhost:6379";s:11:"frontend_db";s:1:"2";s:14:"backend_server";s:14:"localhost:6379";s:10:"backend_db";s:1:"3";}',
			'documentation' => 'a:0:{}',
			'extension_builder' => 'a:3:{s:15:"enableRoundtrip";s:0:"";s:15:"backupExtension";s:1:"1";s:9:"backupDir";s:35:"uploads/tx_extensionbuilder/backups";}',
			'feedit' => 'a:0:{}',
			'indexed_search' => 'a:18:{s:8:"pdftools";s:9:"/usr/bin/";s:8:"pdf_mode";s:2:"20";s:5:"unzip";s:9:"/usr/bin/";s:6:"catdoc";s:9:"/usr/bin/";s:6:"xlhtml";s:9:"/usr/bin/";s:7:"ppthtml";s:9:"/usr/bin/";s:5:"unrtf";s:9:"/usr/bin/";s:9:"debugMode";s:1:"0";s:18:"fullTextDataLength";s:1:"0";s:23:"disableFrontendIndexing";s:1:"0";s:21:"enableMetaphoneSearch";s:1:"1";s:6:"minAge";s:2:"24";s:6:"maxAge";s:1:"0";s:16:"maxExternalFiles";s:1:"5";s:26:"useCrawlerForExternalFiles";s:1:"0";s:11:"flagBitMask";s:3:"192";s:16:"ignoreExtensions";s:0:"";s:17:"indexExternalURLs";s:1:"0";}',
			'info' => 'a:0:{}',
			'jquerycolorbox' => 'a:0:{}',
			'linkvalidator' => 'a:0:{}',
			'opendocs' => 'a:0:{}',
			'phpunit' => 'a:6:{s:17:"excludeextensions";s:8:"lib, div";s:12:"composerpath";s:0:"";s:13:"selenium_host";s:9:"localhost";s:13:"selenium_port";s:4:"4444";s:16:"selenium_browser";s:8:"*firefox";s:19:"selenium_browserurl";s:0:"";}',
			'realurl' => 'a:5:{s:10:"configFile";s:26:"typo3conf/realurl_conf.php";s:14:"enableAutoConf";s:1:"1";s:14:"autoConfFormat";s:1:"1";s:12:"enableDevLog";s:1:"0";s:19:"enableChashUrlDebug";s:1:"0";}',
			'recycler' => 'a:0:{}',
			'rtehtmlarea' => 'a:13:{s:21:"noSpellCheckLanguages";s:23:"ja,km,ko,lo,th,zh,b5,gb";s:15:"AspellDirectory";s:15:"/usr/bin/aspell";s:17:"defaultDictionary";s:2:"en";s:14:"dictionaryList";s:2:"en";s:20:"defaultConfiguration";s:105:"Typical (Most commonly used features are enabled. Select this option if you are unsure which one to use.)";s:12:"enableImages";s:1:"1";s:20:"enableInlineElements";s:1:"0";s:19:"allowStyleAttribute";s:1:"1";s:24:"enableAccessibilityIcons";s:1:"0";s:16:"enableDAMBrowser";s:1:"0";s:16:"forceCommandMode";s:1:"0";s:15:"enableDebugMode";s:1:"0";s:23:"enableCompressedScripts";s:1:"1";}',
			'saltedpasswords' => 'a:2:{s:3:"FE.";a:2:{s:7:"enabled";s:1:"1";s:21:"saltedPWHashingMethod";s:28:"tx_saltedpasswords_salts_md5";}s:3:"BE.";a:1:{s:21:"saltedPWHashingMethod";s:28:"tx_saltedpasswords_salts_md5";}}',
			'scheduler' => 'a:4:{s:11:"maxLifetime";s:4:"1440";s:11:"enableBELog";s:1:"1";s:15:"showSampleTasks";s:1:"1";s:11:"useAtdaemon";s:1:"0";}',
			'sys_action' => 'a:0:{}',
			'workspace_test' => 'a:0:{}',
			'workspaces' => 'a:0:{}',
			'wt_spamshield' => 'a:10:{s:12:"useNameCheck";s:1:"0";s:12:"usehttpCheck";s:1:"3";s:9:"notUnique";s:0:"";s:13:"honeypodCheck";s:1:"1";s:15:"useSessionCheck";s:1:"1";s:16:"SessionStartTime";s:2:"10";s:14:"SessionEndTime";s:3:"600";s:10:"AkismetKey";s:0:"";s:12:"email_notify";s:0:"";s:3:"pid";s:2:"-1";}',
		),
	),
	'EXTCONF' => array(
		'lang' => array(
			'availableLanguages' => array(),
		),
	),
	'FE' => array(
		'debug' => TRUE,
		'loginSecurityLevel' => 'rsa',
		'pageNotFound_handling' => 'http://typo3-workspace-test-environment.dev/index.php?id=16',
	),
	'GFX' => array(
		'colorspace' => 'RGB',
		'gdlib_png' => 1,
		'im' => 1,
		'im_mask_temp_ext_gif' => 1,
		'im_path' => '/usr/bin/',
		'im_path_lzw' => '/usr/bin/',
		'im_v5effects' => -1,
		'im_version_5' => 'gm',
		'image_processing' => 1,
		'jpg_quality' => '80',
	),
	'INSTALL' => array(
		'wizardDone' => array(
			'TYPO3\CMS\Install\Updates\FileIdentifierHashUpdate' => 1,
			'TYPO3\CMS\Install\Updates\FilemountUpdateWizard' => 1,
			'TYPO3\CMS\Install\Updates\TceformsUpdateWizard' => 'tt_content:image,pages:media,pages_language_overlay:media',
			'TYPO3\CMS\Install\Updates\TruncateSysFileProcessedFileTable' => 1,
		),
	),
	'SYS' => array(
		'caching' => array(
			'cacheConfigurations' => array(
				'extbase_object' => array(),
			),
		),
		'compat_version' => '6.2',
		'debugExceptionHandler' => '',
		'devIPmask' => '*',
		'displayErrors' => TRUE,
		'enableDeprecationLog' => 'file',
		'encryptionKey' => '934661a4f28ad1f055e0d1c5c282cead50171a604c030537365d5480a40f368e64fb1189f1e0bd35df2ffa3fa503849b',
		'errorHandlerErrors' => 0,
		'exceptionErrors' => 0,
		'exceptionalErrors' => 0,
		'sitename' => 'Workspaces + Versioning test site',
		'sqlDebug' => 1,
		'systemLogLevel' => 0,
		't3lib_cs_convMethod' => 'mbstring',
		't3lib_cs_utils' => 'mbstring',
	),
);
?>