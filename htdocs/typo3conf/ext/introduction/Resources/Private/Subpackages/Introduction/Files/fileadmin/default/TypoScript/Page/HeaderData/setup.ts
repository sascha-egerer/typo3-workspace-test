/*
The PAGE HEADERDATA template.

All config.headerData contents goes in here. This could be almost everything
that belongs in the <head>, but does not have any configuration like
page.config, page.includeCSS or page.includeJS or needs special
configuration, like dynamic handling.
*/

page.headerData {
	10 = TEMPLATE
	10 {
		template =< plugin.tx_automaketemplate_pi1
		workOnSubpart = DOCUMENT_HEADER
	}
	20 = TEXT
	20.value = <meta name="robots" content="noindex,follow" />
}
