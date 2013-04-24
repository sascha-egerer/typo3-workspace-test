# Add the following uncommented line, to the root sys_template Setup to include this file
# <INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/setup.ts">


# Read in the templates for changes in tt_content, lib.stdHeader etc.
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/System/CssStyledContent/setup.ts">

# Read in the templates for the extensions.
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Extension/AutomakeTemplate/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Extension/FeLogin/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Extension/IndexedSearch/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Extension/TxNews/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Extension/JqueryColorbox/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Extension/WtSpamshield/setup.ts">

# Read in the templates for the menu parts, like the default menu setup, language menu, header- and footermenu's.
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Menu/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Menu/FirstLevel/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Menu/SecondLevel/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Menu/Top/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Menu/Language/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Menu/Breadcrumb/setup.ts">

# Read in the Lib (Blocks) templates containing TypoScript to generate the building blocks for your website, except menu's.
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Block/Content/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Block/ContentLeft/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Block/ContentRight/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Block/SearchBox/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Block/SiteTitle/setup.ts">	
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Block/Footer/setup.ts">

# Read in the templates for the page, like page setup, config and header data.
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Page/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Page/Config/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Page/Meta/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Page/HeaderData/setup.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Page/IncludeCss/setup.ts">
