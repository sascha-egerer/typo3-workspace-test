/*
The AUTOMAKETEMPLATE template

The automaketemplate parser reads the HTML template files, extracts the markers
for content, and replaces them with the content assigned to these markers.
*/

# Configuring the Auto-Parser for main template:

plugin.tx_automaketemplate_pi1 {
	# Read the template file
	content = FILE
	content.file = {$filepaths.templates}{$plugin.tx_automaketemplate_pi1.templatefile}

	elements {
		BODY.all = 1
		BODY.all.subpartMarker = DOCUMENT_BODY
		HEAD.all = 1
		HEAD.all.subpartMarker = DOCUMENT_HEADER

		# Configure which HTML-tags should be made replacable by subparts
		DIV.id.navigationFirstLevelMenu = 1
		DIV.id.navigationSecondLevelMenu = 1
		DIV.id.topMenu = 1
		DIV.id.languageMenu = 1
		DIV.id.breadcrumb = 1

		DIV.id.siteTitle = 1
		DIV.id.searchBox = 1
		DIV.id.footerContent = 1

		DIV.id.mainContent = 1
		DIV.id.secondaryContent = 1
		DIV.id.navigationContent = 1

		H1.all = 1

		# Remove some tags from HTML head section (because TYPO3 will add these dynamically)
		HEAD.rmTagSections = title
		HEAD.rmSingleTags = meta,link
	}

	# Prefix all relative paths in the HTML template with this value
	#relPathPrefix = {$filepaths.templates}
}
