/*
The LANGUAGE menu

Generates a list of available languages on a page. If the page has no language
overlay, there is no link available to switch to the language.
*/

menu.language = HMENU

# We use a condition to show the language menu only on the page with the id from constant contentpage.examplelanguagesID
[globalVar = TSFE:id = {$contentpage.examplelanguagesID}]
menu.language {
	special = language
	special.value = 0,1
	1 = TMENU
	1 {
		wrap = <ul>|</ul>

		NO = 1
		NO {
			# Override the standard menu item value (which is the page title) with our own cObject
			stdWrap.cObject = TEXT

			# Use the TypoScript option split function to display different values for the first and second item
			stdWrap.cObject.value = {$contentpage.language1} || {$contentpage.language2}
			allWrap = <li>|</li>
		}

		ACT < .NO
		ACT {
			ATagParams = class="active"
		}
	}
}
[global]
