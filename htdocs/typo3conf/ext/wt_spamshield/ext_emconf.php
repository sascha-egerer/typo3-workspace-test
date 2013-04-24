<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "wt_spamshield".
 *
 * Auto generated 22-04-2013 14:55
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Spamshield',
	'description' => 'Spam shield without captcha to avoid spam in powermail, ve_guestbook, comments, t3_blog and standard TYPO3 mailforms. Session check, Link check, Time check, Akismet check, Name check, Honeypot check (see manual for details)',
	'category' => 'services',
	'shy' => 0,
	'version' => '0.9.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Alex Kellner, Bjoern Jacob',
	'author_email' => 'alexander.kellner@in2code.de, bjoern.jacob@tritum.de',
	'author_company' => 'in2code, TRITUM',
	'CGLcompliance' => NULL,
	'CGLcompliance_note' => NULL,
	'constraints' => 
	array (
		'depends' => 
		array (
			'php' => '4.0.0-0.0.0',
			'typo3' => '3.5.0-0.0.0',
		),
		'conflicts' => '',
		'suggests' => 
		array (
		),
	),
);

?>