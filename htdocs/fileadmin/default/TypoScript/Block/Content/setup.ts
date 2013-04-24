/*
The CONTENT template

This template will fill the part 'content' in the HTML template. Normally we
just get the content with styles.content.get, but we need the wrapper around it
for indexed search, so it knows what to index.
*/

# Insert the news plugin in single-view mode instead of normal page content if a news article is requested
[globalVar = GP:tx_news_pi1|news > 0]
	lib.content < lib.news_display
[else]
	# In all other cases, get the content of the middle column and add it to the 'content' part
	lib.content < styles.content.get
	lib.content.stdWrap.replacement {
		1 {
			search = ###BACKEND_URL###
			replace.typolink {
				parameter = typo3/
				returnLast = url
			}
		}
	}
[global]

lib.content {
	# Wrap it in the markers for the search engine, so it knows that this part has to be indexed
	stdWrap.wrap = <!--TYPO3SEARCH_begin--> | <!--TYPO3SEARCH_end-->
}
