/*
The FIRST LEVEL MENU template.

Configuration for the menu of the first level of pages. It inherits the MAIN
MENU template and has some small modifications on top of it, in this case the
setting of the entryLevel.
*/

# Start with copying our default menu configuration so we don't have to repeat our selves (except in the explanation, didn't I already mention this?) ;-)
menu.firstlevel < menu.template
menu.firstlevel {
	# Since this is the top-level menu, we start this menu at the root level of the website
	entryLevel = 0
}

[globalVar = TSFE:id = 1]
menu.firstlevel {
	# PID 6 is a shortcut to home, show it as active
	alwaysActivePIDlist = 6
}
[end]
