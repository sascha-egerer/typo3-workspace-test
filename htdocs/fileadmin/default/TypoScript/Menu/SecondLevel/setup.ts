/*
The SECOND LEVEL MENU template.

Configuration for the menu of the sublevels of pages. It inherits the MAIN MENU
template and has some small modifications on top of it, in this case the
settings of the entryLevel.
*/

# This menu is slightly more complicated than the others because we copy our default menu again, but then we'll start overriding some settings from the default menu.
# To get a full overview of the configuration of this menu you should look at both our default 'menu' and the 'menu.secondlevel' as shown below.
# You can use the TypoScript Object Browser to view the end result of this.
# (go to the Template module and select 'Typoscript Object Browser' in the selectbox in the right frame.)

menu.secondlevel < menu.template
menu.secondlevel {
	entryLevel = 1

	1 {
		NO {
			wrapItemAndSub = <li>|</li>
		}

		# First clear the ATagParams setting of the ACT state because we are working with a copy of the object 'menu'
		# After that we add our modified configuration for menu.secondlevel
		ACT {
			ATagParams >
			wrapItemAndSub = <li class="active">|</li>
		}

		CUR {
			ATagParams >
			wrapItemAndSub = <li class="selected">|</li>
		}
	}

	# Copy the configuration of the first level of this menu to level 2 and 3
	2 < .1
	3 < .1
}
