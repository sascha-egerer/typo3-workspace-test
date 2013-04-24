/*
The SITE TITLE template

Building block for the site title
*/

lib.sitetitle = TEXT
lib.sitetitle {
	# Copy the value from the top level object sitetitle
	# You can change the site title in the Sitetitle field of the ROOT typoscript template
	value < sitetitle

	# Wrap a link to the home page around the sitetitle
	typolink.parameter = {$contentpage.homeID}
}