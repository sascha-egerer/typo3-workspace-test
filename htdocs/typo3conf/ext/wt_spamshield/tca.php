<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_wtspamshield_log'] = array (
    'ctrl' => $TCA['tx_wtspamshield_log']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'form,errormsg,formvalues,pageid,ip,useragent'
    ),
    'feInterface' => $TCA['tx_wtspamshield_log']['feInterface'],
    'columns' => array (
        'form' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_log.form',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'required',
            )
        ),
        'errormsg' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_log.errormsg',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'required',
            )
        ),
		'formvalues' => array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_log.formvalues',        
            'config' => array (
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'wizards' => array (
                    '_PADDING' => 2,
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'LLL:EXT:powermail/locallang_db.xml:tx_powermail_mails.content_RTE',
                        'icon' => 'wizard_rte2.gif',
                        'script' => 'wizard_rte.php',
                    ),
                ),
            )
        ),
        'pageid' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_log.pageid',        
            'config' => Array (
                'type' => 'input',    
                'size' => '5',
            )
        ),
        'ip' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_log.ip',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',
            )
        ),
        'useragent' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_log.useragent',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',
            )
        ),
    ),
    'types' => array (
        '0' => array('showitem' => 'form;;;;1-1-1, errormsg, formvalues;;;richtext[], pageid, ip, useragent')
    ),
    'palettes' => array (
        '1' => array('showitem' => '')
    )
);

$TCA['tx_wtspamshield_blacklist'] = array (
    'ctrl' => $TCA['tx_wtspamshield_blacklist']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'type, value'
    ),
    'feInterface' => $TCA['tx_wtspamshield_log']['feInterface'],
    'columns' => array (
        /*'whitelist' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_blacklist.whitelist',
            'config' => Array (
                'type' => 'check',
            )
        ),*/
		'type' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_blacklist.type',
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_blacklist.type.0', 'ip'),
					array('LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_blacklist.type.1', 'email'),
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
        'value' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:wt_spamshield/locallang_db.xml:tx_wtspamshield_blacklist.value',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
            )
        ),
    ),
    'types' => array (
        '0' => array('showitem' => 'type, value')
    ),
    'palettes' => array (
        '1' => array('showitem' => '')
    )
);
?>