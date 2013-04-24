/*
TypoScript template which compiles the contents for the left column on the pages.
*/

lib.contentleft = COA

# Insert the content from the left column into lib.contentleft
lib.contentleft {
	20 < styles.content.getLeft
}
