/*
The Tx_News template

The name says it all, this is a news plugin.

For a full description of the configuration options, check the manual of this
extension. Extension manuals are normally included in the extensions as Open
Office documents, and can be accessed directly from the module Extension
Manager in the TYPO3 backend (select the option 'loaded extensions')
*/

<INCLUDE_TYPOSCRIPT: source="EXT:news/Configuration/TypoScript/setup.txt">

plugin.tx_news {

	# Allow more than one plugin on the same page with different views (actions)
	mvc.callDefaultActionIfActionCantBeResolved = 1

	/*
	Change the template, partial and layout root path so we can customize it.
	All folders are copies of the ones from EXT:news/Resources/Private/
	*/
	view {
		templateRootPath = fileadmin/default/templates/extensions/tx_news/Templates/
		partialRootPath = fileadmin/default/templates/extensions/tx_news/Partials/
		layoutRootPath = fileadmin/default/templates/extensions/tx_news/Layouts/
	}

	settings {
		# do not display a dummy image if the record does not contain an image
		displayDummyIfNoMedia = 0

		# settings for list view
		list {
			media {
				# limit image sizes (px)
				image {
					maxWidth = 175
					maxHeight = 175
				}
			}
		}

		# settings for detail view
		detail {
			media {
				# limit image sizes (px)
				image {
					maxWidth = 250
					maxHeight = 300
				}
			}
		}

	}

}

/*
Definition for the single display view. This replaces lib.content if a single news item is requested.
Have a look at fileadmin/default/TypoScript/Block/Content/setup.ts on how this is used.
*/
lib.news_display = USER
lib.news_display {
	userFunc = tx_extbase_core_bootstrap->run
	pluginName = Pi1
	extensionName = News
	controller = News
	settings =< plugin.tx_news.settings
	persistence =< plugin.tx_news.persistence
	view =< plugin.tx_news.view
	action = detail
	switchableControllerActions.News.1 = detail
}
