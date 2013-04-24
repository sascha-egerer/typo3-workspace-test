/*
The FELOGIN template

The felogin extension handles the login for access restricted frontend pages.
It displays a form when the user is not logged in, a status when logged in and
handles the login process.

For a full description of the configuration options, check the manual of this
extension. Extension manuals are normally included in the extensions as Open
Office documents, and can be accessed directly from the module Ext Manager in
the TYPO3 backend (select the option 'loaded extensions or install extension').
*/

plugin.tx_felogin_pi1 {
	# Use our own HTML template from the fileadmin directory
	# so we can freely modify it without changing the extension
	templateFile = {$filepaths.extensiontemplates}felogin/tx_felogin_pi1_template.html

	# Clear default CSS additions - we take care of that in our own CSS files
	_CSS_DEFAULT_STYLE =
}
