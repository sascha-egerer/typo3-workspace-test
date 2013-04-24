/*
The CSS template.

Includes all necessarry Cascading Style Sheets by referring to the file on the
server and configuring the media type.

Special condition for including extra CSS file for Internet Explorer.
*/

page.includeCSS {
	10 = {$filepaths.css}stylesheet.css
	10 {
		media = all
	}
	20 = {$filepaths.css}print.css
	20 {
		media = print
	}
}

# Condition to add specific stylesheet for the frontpage only
[globalVar = TSFE:id = 1]
page.includeCSS {
	30 = {$filepaths.css}frontpage.css
	30 {
		media = all
	}
}
[end]

# Condition to add extra CSS for Internet Explorer 6
[browser = msie] && [version =< 7]
page.includeCSS {
	9999 = {$filepaths.css}msie6.css
	9999 {
		media = screen
	}
}
[global]
