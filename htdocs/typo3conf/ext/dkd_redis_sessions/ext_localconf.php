<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Add the service
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
	$_EXTKEY,
	'sessionStorage',
	'TYPO3\\CMS\\DkdRedisSessions\\FrontendStorage',
	array(
		'title' => 'Session Storage: Redis',
		'description' => 'Stores user sessions in Redis (http://redis.io)',
		'subtype' => 'frontend',
		'available' => TRUE,
		'priority' => 100,
		'quality' => 50,
		'os' => '',
		'exec' => '',
		'className' => 'TYPO3\\CMS\\DkdRedisSessions\\FrontendStorage'
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
	$_EXTKEY,
	'sessionStorage',
	'TYPO3\\CMS\\DkdRedisSessions\\BackendStorage',
	array(
		'title' => 'Session Storage: Redis',
		'description' => 'Stores user sessions in Redis (http://redis.io)',
		'subtype' => 'backend',
		'available' => TRUE,
		'priority' => 100,
		'quality' => 50,
		'os' => '',
		'exec' => '',
		'className' => 'TYPO3\\CMS\\DkdRedisSessions\\BackendStorage'
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43(
	'dkd_redis_sessions',
	'Classes/Plugin/Frontendtest.php',
	'_frontendtest'
);

class_alias('TYPO3\\CMS\\DkdRedisSessions\\Plugin\\Frontendtest', 'tx_dkdredissessions_frontendtest');