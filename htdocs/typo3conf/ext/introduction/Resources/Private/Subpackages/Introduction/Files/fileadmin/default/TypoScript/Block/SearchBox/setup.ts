/*
The SEARCHBOX template

A building block for a searchform
*/

# Create the searchbox in TypoScript, so we can easily include in on every page
lib.searchbox = COA

# Make the searchbox remember the search term on the search result page
[globalVar = TSFE:id = 17]
lib.searchbox = COA_INT
[global]

lib.searchbox {
	stdWrap.prefixComment = 2 | lib.searchbox
	10 = TEXT
	10.typolink.parameter = {$plugin.tx_indexedsearch.searchpageID}
	10.typolink.returnLast = url
	10.wrap = <form action="|" method="post" id="indexedsearch"><fieldset>
	20 = COA
	20 {
		10 = TEXT
		10.data = GP : tx_indexedsearch |sword
		10.htmlSpecialChars = 1
		10.wrap = <input name="tx_indexedsearch[sword]" value="|" id="inputText" type="text" />
		20 = COA
		20 {
			10 = TEXT
			10.value = <input type="image" src="{$filepaths.templates}images/bg_searchButton.gif" id="inputButton" alt="Search" title="Search" />
			20 = TEXT
			20.value = <input type="hidden" name="tx_indexedsearch[sections]" value="0" />
			30 = TEXT
			30.value = <input name="tx_indexedsearch[submit_button]" value="Search" type="hidden" />
		}
	}

	wrap = |</fieldset></form>
}