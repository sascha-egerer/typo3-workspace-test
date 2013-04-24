<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

/* Using HOOKS in other extensions */

// Hook Powermail: Generating Form
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FormWrapMarkerHook'][] = 'EXT:wt_spamshield/ext/class.tx_wtspamshield_powermail.php:tx_wtspamshield_powermail';

// Hook Powermail: Giving error to Powermail
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitBeforeMarkerHook'][] = 'EXT:wt_spamshield/ext/class.tx_wtspamshield_powermail.php:tx_wtspamshield_powermail';

// Hook ve_guestbook: Generating Form
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ve_guestbook']['extraItemMarkerHook'][] = 'EXT:wt_spamshield/ext/class.tx_wtspamshield_ve_guestbook.php:tx_wtspamshield_ve_guestbook';

// Hook ve_guestbook: Giving error to guestbook
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ve_guestbook']['preEntryInsertHook'][] = 'EXT:wt_spamshield/ext/class.tx_wtspamshield_ve_guestbook.php:tx_wtspamshield_ve_guestbook';

// Hook standard mailform: Disabling email
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['sendFormmail-PreProcClass'][] = 'EXT:wt_spamshield/ext/class.tx_wtspamshield_defaultmailform.php:tx_wtspamshield_defaultmailform';
// ToDo: Does not work with new extbase based form extension
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form/Classes/Controller/Form.php']['showForm'][] = 'EXT:wt_spamshield/ext/class.tx_wtspamshield_defaultmailform.php:tx_wtspamshield_defaultmailform->handleSystemExtensionForm';

// Hook tx_comments: Generating Form
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['comments']['form'][] = 'EXT:wt_spamshield/ext/class.tx_wtspamshield_comments.php:tx_wtspamshield_comments->form';

// Hook tx_comments: Giving error to comments
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['comments']['externalSpamCheck'][] = 'EXT:wt_spamshield/ext/class.tx_wtspamshield_comments.php:tx_wtspamshield_comments->externalSpamCheck';

// Hook tx_keuserregister: Generating Form
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_keuserregister']['additionalMarkers'][] = 'EXT:wt_spamshield/ext/class.tx_wtspamshield_ke_userregister.php:tx_wtspamshield_ke_userregister';

// Hook ke_userregister: Giving error to ke_userregister
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_keuserregister']['specialEvaluations'][] = 'EXT:wt_spamshield/ext/class.tx_wtspamshield_ke_userregister.php:tx_wtspamshield_ke_userregister';

// Hook t3_blog
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['t3blog']['aftercommentinsertion'][] = 'EXT:wt_spamshield/ext/class.tx_wtspamshield_t3blog.php:tx_wtspamshield_t3blog->insertNewComment';
?>