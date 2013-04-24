<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Alexander Kellner <Alexander.Kellner@einpraegsam.net>
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib . 'class.tslib_pibase.php');

class tx_wtspamshield_mail extends tslib_pibase {

	var $extKey = 'wt_spamshield'; // Extension key of current extension
	var $sendEmail = 1; // Disable email sending for testing
	
	/**
	 * Function sendEmail sends a notify mail to the admin if spam was recognized
	 * 
	 * @param	string		$ext: Name of extension in which the spam was recognized
	 * @param	string		$error: Error Message
	 * @param	array		$formArray: Array with submitted values
	 * @param	boolean		$sendPlain: Plain instead of HTML mails
	 * @return	void
	 */
	function sendEmail($ext, $error, $formArray, $sendPlain = 1) {
		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]); // Get backend configuration of this extension
		
		if (isset($conf)) { // Only if Backendconfiguration exists in localconf
			if (t3lib_div::validEmail($conf['email_notify'])) { // Only if email address is valid
				if (!$sendPlain) { // html mail
				
					// Prepare mail
					$mailtext = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
						<html>
							<head>
							</head>
							<body>
								<table>
									<tr>
										<td><strong>Extension:</strong></td>
										<td>' . $ext . '</td>
									</tr>
									<tr>
										<td><strong>PID:</strong></td>
										<td>' . $GLOBALS['TSFE']->id . '</td>
									</tr>
									<tr>
										<td><strong>URL:</strong></td>
										<td>' . t3lib_div::getIndpEnv('HTTP_HOST') . '</td>
									</tr>
									<tr>
										<td><strong>Error:</strong></td>
										<td>' . $error . '</td>
									</tr>
									<tr>
										<td><strong>IP:</strong></td>
										<td>' . t3lib_div::getIndpEnv('REMOTE_ADDR') . '</td>
									</tr>
									<tr>
										<td><strong>Useragent:</strong></td>
										<td>' . t3lib_div::getIndpEnv('HTTP_USER_AGENT') . '</td>
									</tr>
									<tr>
										<td valign=top><strong>Form values:</strong></td>
										<td>' . t3lib_div::view_array($formArray) . '</td>
									</tr>
								</table>
							</body>
						</html>
					';
					
					// Send mail
					$this->htmlMail = t3lib_div::makeInstance('t3lib_htmlmail');
					$this->htmlMail->start();
					$this->htmlMail->recipient = $conf['email_notify'];
					$this->htmlMail->subject = 'Spam recognized in ' . $ext . ' on ' . t3lib_div::getIndpEnv('HTTP_HOST');
					$this->htmlMail->from_email = $conf['email_notify'];
					$this->htmlMail->from_name = 'Spamshield';
					$this->htmlMail->returnPath = $conf['email_notify'];
					$this->htmlMail->setHTML($mailtext);
					if ($this->sendEmail) {
						$this->htmlMail->send($conf['email_notify']); // send mail now
					}
				
				} else { // plaintextmail
				
					$info = array(
						'Extension' => $ext,
						'PID' => $GLOBALS['TSFE']->id,
						'URL' => t3lib_div::getIndpEnv('HTTP_HOST'),
						'Error' => $error,
						'IP' => t3lib_div::getIndpEnv('REMOTE_ADDR'),
						'Useragent' => t3lib_div::getIndpEnv('HTTP_USER_AGENT'),
					);
					foreach ($info as $key => $value) {
						$mailtext .= $key . ': ' . $value . chr(10);
					}
					$mailtext .= chr(10) . 'Form values:' . chr(10);
					foreach ($formArray as $key => $value) {
						$mailtext .= ' * ' . $key . ': ' . $value . chr(10);
					}
					
					$to = $conf['email_notify'];
					$from = '"Spamshield" <' . $conf['email_notify'] . '>';
					$subject = 'Spam recognized in ' . $ext . ' on ' . t3lib_div::getIndpEnv('HTTP_HOST');
					$headers = 'From: ' . $from;
					//$headers .= 'Reply-To: ' . $from;
					//t3lib_div::plainMailEncoded($to, $subject, $mailtext, $headers); // send plaintextmail
					mail($to, $subject, $mailtext, $headers); // send plaintextmail
				}
			}
		}
			
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/functions/class.tx_wtspamshield_mail.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_spamshield/functions/class.tx_wtspamshield_mail.php']);
}

?>