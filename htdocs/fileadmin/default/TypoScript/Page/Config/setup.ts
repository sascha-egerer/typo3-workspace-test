/*
The PAGE CONFIG template.

Only page.config settings are done here, like character sets, url and language.
*/

config {
	// Administrator settings
	admPanel = {$config.adminPanel}
	debug = {$config.debug}

	doctype = html5
	htmlTag_setParams = none
	// Include Boilerplate handling for IE browsers
	htmlTag_stdWrap {
		setContentToCurrent = 1
		cObject = COA
		cObject {
			10 = LOAD_REGISTER
			10 {
				newLine.char = 10
				tagEnd {
					current = 1
					split.max = 2
					split.token = <html
					split.returnKey = 1
				}
			}

			20 = TEXT
			20.value = <!--[if lt IE 7]> <html class="no-js ie6 oldie"{register:tagEnd} <![endif]-->
			20.wrap = |{register:newLine}
			20.insertData = 1
			30 < .20
			30.value = <!--[if IE 7]> <html class="no-js ie7 oldie"{register:tagEnd} <![endif]-->
			40 < .20
			40.value = <!--[if IE 8]> <html class="no-js ie8 oldie"{register:tagEnd} <![endif]-->
			50 < .20
			50.value = <!--[if gt IE 8]> <!--><html class="no-js ie6 oldie"{register:tagEnd} <!--<![endif]-->

			90 = RESTORE_REGISTER
		}
	}

	// Character sets
	renderCharset = utf-8
	metaCharset = utf-8

	// Cache settings
	cache_period = 43200
	sendCacheHeaders = 1

	// URL Settings
	tx_realurl_enable = 1
	simulateStaticDocuments = 0

	// Language Settings
	uniqueLinkVars = 1
	linkVars = L(1)
	sys_language_uid = 0
	sys_language_overlay = 1
	sys_language_mode = content_fallback
	language = en
	locale_all = en_US.UTF-8
	htmlTag_langKey = en

	// Link settings
	absRefPrefix = {$config.absRefPrefix}
	prefixLocalAnchors = all

	// Remove targets from links
	intTarget =
	extTarget =

	// Indexed Search
	index_enable = 1
	index_externals = 1

	// Code cleaning
	disablePrefixComment = 1

	// Move default CSS and JS to external file
	removeDefaultJS = external
	inlineStyle2TempFile = 1

	// Protect mail addresses from spamming
	spamProtectEmailAddresses = -3
	spamProtectEmailAddresses_atSubst = @<span style="display:none;">remove-this.</span>

	// Comment in the <head> tag
	headerComment (
				We <3 TYPO3

				===
	)
}


// Condition to set language according to L POST/GET variable
[globalVar = GP:L = 1]
config {
	htmlTag_langKey = da
	sys_language_uid = 1
	language = da
	locale_all = da_DK
}
[global]