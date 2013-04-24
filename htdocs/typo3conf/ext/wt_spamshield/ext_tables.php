<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addStaticFile($_EXTKEY, 'static/settings/', 'Main Settings');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/defaultmailform/', 'Default Mailform');

$TCA['tx_wtspamshield_log'] = array (
    'ctrl' => array (
        'title'     => 'LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_log',        
        'label'     => 'errormsg',    
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY crdate DESC',    
        'delete' => 'deleted',    
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_wtspamshield_log.gif',
    ),
    'feInterface' => array (
        'fe_admin_fieldList' => 'form, errormsg, formvalues, pageid, ip, useragent',
    )
);

$TCA['tx_wtspamshield_blacklist'] = array (
    'ctrl' => array (
        'title'     => 'LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_blacklist',
        'label'     => 'value',
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY value ASC',
        'delete' => 'deleted',
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_wtspamshield_log.gif',
    ),
    'feInterface' => array (
        'fe_admin_fieldList' => 'type, value',
    )
);
?>