/*
TypoScript template which compiles the contents for the right column on the pages.
*/

lib.contentright = COA

# The configuration below this condition will only be included on the page loginpageID, or on its subpages
[PIDinRootline = {$contentpage.loginpageID}]

# Insert a loginbox above the content (but below the menu) in the left column.
# We fetch the loginbox record from the SysFolder 'Login Box' under 'Generated content'
# There are different ways to do this, in lib.footer you will see a slightly different approach.
lib.contentright {
	10 = CONTENT
	10 {
		table = tt_content
		select.pidInList = {$contentpage.loginboxPID}
		select.uidInList = {$contentpage.loginboxUID}
	}
}

# End of the conditional part
[global]

# In all other cases, get the content of the middle column and add it to the 'content' part
lib.contentright.20 < styles.content.getRight
