/*
The INDEXED_SEARCH template

Indexed Search an extension for searching content in the frontend.

For a full description of the configuration options, check the manual of this
extension. Extension manuals are normally included in the extensions as Open
Office documents, and can be accessed directly from the module Ext Manager in
the TYPO3 backend (select the option 'loaded extensions or install extension').

For the extension indexed search, the documentation is delivered in a separated
extension with the extension key 'doc_indexed_search', which is also provided
with this Demo Package.
*/

# Configuration for indexedsearch plugin
plugin.tx_indexedsearch {
	templateFile = {$filepaths.extensiontemplates}indexed_search/tx_indexedsearch_pi1_template.html
	forwardSearchWordsInResultLink = 0

	show {
		rules = 0
		advancedSearchLink = 0
	}

	search {
		rootPidList =
		exactCount = 1
	}

	_CSS_DEFAULT_STYLE >
	_DEFAULT_PI_VARS {
		results = 10
	}
}

# Adjust search results when visitor has chosen another language
[globalVar = GP:L = 1]
plugin.tx_indexedsearch._DEFAULT_PI_VARS.lang = 1
[global]
