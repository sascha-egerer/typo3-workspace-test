<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Add the service
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
	$_EXTKEY,
	'sessionStorage',
	'TYPO3\\CMS\\DkdRedisSessions\\RedisSessionStorage',
	array(
		'title' => 'Session Storage: Redis',
		'description' => 'Stores user sessions in Redis (http://redis.io)',
		'subtype' => 'backend, frontend',
		'available' => TRUE,
		'priority' => 100,
		'quality' => 50,
		'os' => '',
		'exec' => '',
		'className' => 'TYPO3\\CMS\\DkdRedisSessions\\RedisSessionStorage'
	)
);
