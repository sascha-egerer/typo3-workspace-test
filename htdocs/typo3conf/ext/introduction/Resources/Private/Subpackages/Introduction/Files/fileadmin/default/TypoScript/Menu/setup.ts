/*
The MENU template.

Configuration for default menu, which can be used with small modifications for
other menu's, without writing the whole menu configuration all over again for
all kinds of menu's.
*/

menu.template = HMENU
menu.template {
	1 = TMENU
	1 {
		wrap = <ul>|</ul>

		# Always unfold all sub-levels of the menu
		expAll = 1

		# Define the normal state (not active, not selected) of menu items
		# Using NO=1 to activate normal state is not necessary, but useful when copying
		NO = 1
		NO {
			# Use the page title field the title property on the A-tag, but only if the navigation title is set
			ATagTitle {
				field = title
				fieldRequired = nav_title
			}

			# Use the option-split feature to generate a different wrap for the last item on a level of the menu
			# The last item on each level gets class="last" added for CSS styling purposes.
			#
			# See the TSref documentation for details about option split and other features:
			# http://typo3.org/documentation/document-library/references/doc_core_tsref/current/
			wrapItemAndSub = <li>|</li> |*| <li>|</li> |*| <li class="last">|</li>

			# HTML-encode special characters according to the PHP-function htmlSpecialChars
			stdWrap.htmlSpecialChars = 1
		}

		# Copy properties of normal to active state, and then add a CSS class for styling
		ACT < .NO
		ACT {
			ATagParams = class="active"
		}

		# Copy properties of normal to current state, and then add a CSS class for styling
		CUR < .NO
		CUR {
			ATagParams = class="selected"
		}
	}
}
