/*
The PAGE OBJECT template. 

Tells the PAGE object to use the parsed HTML template from the automaketemplate extension.
*/

# Make the PAGE object
page = PAGE
page {
	# Regular pages always have typeNum = 0
	typeNum = 0

	# Add the icon that will appear in front of the url in the browser
	# This icon will also be used for the bookmark menu in browsers
	shortcutIcon = {$filepaths.images}favicon.ico

	# Add class to bodytag to select which columns will be used in the HTML template
	# Labels for the values used in this field are defined in the TSconfig field of the root page of the website
	bodyTagCObject = CASE
	bodyTagCObject {
		# The value of the CASE object will depend on the value of the layout field in the page records
		key.field = layout

		# Define the default value
		default = TEXT
		default.value = <body>

		# Copy the default value to 0
		0 < .default

		# Add different values for cases 1, 2 and 3
		1 = TEXT
		1.wrap = <body class="|">
		1.value = hideRightColumn

		2 < .1
		2.value = hideLeftColumn

		3 < .1
		3.value = hideRightAndLeftColumn
	}

	# Add a TEMPLATE object to the page
	# We use the template autoparser extension to easily replace parts of the HTML template by dynamic TypoScript objects
	10 = TEMPLATE
	10 {
		# Use the HTML template from the automake template plugin
		template =< plugin.tx_automaketemplate_pi1

		# Use the <body> subpart
		workOnSubpart = DOCUMENT_BODY

		# Link content and page blocks to id's that have been enabled in the
		# automaketemplate template in the extension_configuration sysfolder
		subparts {
			# Insert menu's from lib-objects into the appropriate subparts
			navigationFirstLevelMenu < menu.firstlevel
			navigationSecondLevelMenu < menu.secondlevel
			topMenu < menu.top
			languageMenu < menu.language
			breadcrumb < menu.breadcrumb

			# Insert various TypoScript lib objects into the appropriate subparts of the template
			siteTitle < lib.sitetitle
			searchBox < lib.searchbox
			footerContent < lib.footer

			# Insert content as already constructed in TypoScript objects into subparts
			mainContent < lib.content
			secondaryContent < lib.contentright
			navigationContent < lib.contentleft
		}
	}
}