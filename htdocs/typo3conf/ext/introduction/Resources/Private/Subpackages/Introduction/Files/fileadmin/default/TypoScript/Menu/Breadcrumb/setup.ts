/*
The BREADCRUMB template

Typoscript for producing a breadcrumb.
*/

# For the breadcrumb cObject we use a HMENU of the type 'rootline'
menu.breadcrumb = COA
menu.breadcrumb {
	10 = HMENU
	10 {
		# Select HMENU type 'special.rootline'
		special = rootline

		# Traverse the pagetree starting at the rootpage (0) and ending at the current page (-1)
		special.range = 0|-1

		# Pages which are excluded from the regular menus should still be shown in the breadcrumb
		includeNotInMenu = 1

		# This menu has only 1 level since this is a rootline-menu
		1 = TMENU
		1 {
			# Append spaces and >> to normal linked breadcrumb items
			NO.allWrap = |&#32;&raquo;&#32;
			NO.stdWrap.htmlSpecialChars = 1
		}
	}
}

# This condition checks whether a news article will be shown in single view
[globalVar = GP:tx_news_pi1|news > 0]
menu.breadcrumb {
	# Render the current page as the normal state (linked) because we'll append the title of the news article
	10.1 {
		CUR = 1
		CUR < .NO
	}

	# Append the title of the news item. Using this example, the breadcrumb can be extended with
	# any other thinkable kind of data and logic
	20 = RECORDS
	20 {
		if.isTrue.data = GP:tx_news_pi1|news
		dontCheckPid = 1
		tables = tx_news_domain_model_news
		source.data = GP:tx_news_pi1|news
		source.intval = 1
		conf.tx_news_domain_model_news = TEXT
		conf.tx_news_domain_model_news {
			field = title
			htmlSpecialChars = 1
			typolink {
				parameter.data = page:uid
				addQueryString = 1
			}
		}
		wrap =  <span>|</span>
	}
}
# Else configure the breadcrumb for normal cases when no news article is shown
[else]
menu.breadcrumb {
	10.1 {
		# Add alternative, unlinked configuration for current page, which is always the last item in
		# the breadcrumb
		CUR = 1
		CUR.stdWrap.htmlSpecialChars = 1
		CUR.allWrap = <span>|</span>

		# Do not wrap a link around this item
		CUR.doNotLinkIt = 1
	}
}
[global]
