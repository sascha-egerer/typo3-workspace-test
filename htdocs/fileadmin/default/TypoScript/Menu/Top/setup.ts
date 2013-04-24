/*
The MAIN MENU template.

Configuration for the top right menu.
*/

# Copy basic menu object from TS template 'menu' so we don't have to repeat all that
menu.top < menu.template
menu.top {
	# Also show pages which have not-in-menu flag set in page properties
	includeNotInMenu = 1

	# Create a menu out of the pages specifically listed under special.value
	special = list
	special.value = {$menu.top.pages}
}