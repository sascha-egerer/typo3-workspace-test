<?php

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
	'tt_content',
	'list_type',
	array(
		'Redis Session Storage: FE test plugin',
		'dkd_redis_sessions_frontendtest',
		'EXT:dkd_redis_sessions:ext_icon.gif'
	)
);