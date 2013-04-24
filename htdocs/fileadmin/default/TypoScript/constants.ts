# Add the following uncommented line, to the root sys_template Constants to include this file
# <INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/constants.ts">


<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/System/CssStyledContent/constants.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Extension/JqueryColorbox/constants.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:fileadmin/default/TypoScript/Extension/WtSpamshield/constants.ts">

filepaths {
	# cat=filepaths; type=string; label=HTML Templates: Location of the (X)HTML templates relative to site
	templates = fileadmin/default/templates/

	# cat=filepaths; type=string; label=CSS: Location of the Cascading Style Sheets relative to site
	css = fileadmin/default/templates/css/

	# cat=filepaths; type=string; label=Images: Location of the images relative to site
	images = fileadmin/default/templates/images/

	# cat=filepaths; type=string; label=Scripts: Location of the Javascript files relative to site
	scripts = fileadmin/default/templates/scripts/

	# cat=filepaths; type=string; label=HTML Templates for extensions: Location of the (X)HTML templates for extensions
	extensiontemplates = fileadmin/default/templates/extensions/
}

menu {
	# cat=navigation menus; type=string; label= Top-menu pages: Comma separated list of page id's to be included in top-right menu.
	top.pages = 28
}

plugin.tx_automaketemplate_pi1 {
	# cat=plugin.automaketemplate; type:string; label= HTML template file: Name of the HTML page template file (without path, see contant for filepath.templates)
	templatefile = typo3-intro-template.html
}

plugin.tx_indexedsearch {
	# cat=plugin.indexed_search; type=int+; label= Search Page ID: UID of the page which contains the indexed search plugin.
	searchpageID = 17
}

plugin.meta {
	# cat=plugin.meta; type=string; label= Description: Write a short abstract for your website.
	description = This TYPO3 Introduction Package helps you to get started with TYPO3. It provides a basic website setup with examples for the most used features. On top of that, this package contains many helpful comments and notes on how the system works

	# cat=plugin.meta; type=string; label= Keywords: Enter a comma separated list of keywords.
	keywords = TYPO3, introduction package, install, demo site, example setup, content management, enterprise

	# cat=plugin.meta; type=string; label= Robots: Use for instance these codes: Index all pages: "all".  Index no pages: "none". Only this page: "index,nofollow".  Only subpages: "noindex,follow"
	robots = all

	# cat=plugin.meta; type=string; label= Reply-to email
	email =

	# cat=plugin.meta; type=string; label= Author: Enter name of author.
	author = T3UXW09 / typo3.org

	# The meta tags below are not used on this website, if you want to use these, you can configure that in TypoScript template page.meta

	# cat=plugin.meta; type=boolean; label=Always include global.
	includeGlobal = 0

	# cat=plugin.meta; type=options[,Arabic=ar,Chinese=zh,Danish=dk,Dutch=nl,English=en,Finnish=fi,French=fr,German=de,Greek=el,Hebrew=he,Icelandic=is,Italian=it,Japanese=ja,Norwegian=no,Polish=pl,Portuguese=pt,Russian=ru,Spanish=es,Swedish=sv,Turkish=tr,Multi language=mul]; label= Language: Select language of the content.
	language = en

	# cat=plugin.meta; type=string; label= Distribution
	distribution = GLOBAL

	# cat=plugin.meta; type=options[,General,Mature,14 years,Restricted]; label= Rating
	rating = General
}

styles.content {
	# This defines the maximum width of images inserted in content records of type Images or Text-with-images.
	# There are seperate settings for images floated next to text (..InText)
	imgtext {
		maxW = 651
		maxWInText = 651
		borderThick = 1
		linkWrap.newWindow = 1
	}
	uploads {
		jumpurl_secure = 1
		jumpurl_secure_mimeTypes = pdf=application/pdf, doc=application/msword
		jumpurl = 1
	}
}

contentpage {
	# cat=contentpage; type=int+; label= Footer source PID: Parent ID of the footer record used on content pages.
	footerPID = 20

	# cat=contentpage; type=int+; label= ID of the home page: ID of the home (root) page of the site.
	homeID = 1

	# cat=contentpage; type=int+; label= loginboxPID: ID of the folder containing the login box record (to be shown on multiple pages)
	loginboxPID = 21

	# cat=contentpage; type=int+; label= loginboxUID: UID of the login box record (to be shown on multiple pages)
	loginboxUID = 31

	# cat=contentpage; type=int+; label= loginpageID: UID of the customer login page.
	loginpageID = 28

	# cat=contentpage; type=int+; label= examplelanguagesID: UID of the page which has the language menu enabled
	examplelanguagesID = 25

	# cat=contentpage; type=string; label= language1: name of the default language of this site
	language1 = English

	# cat=contentpage; type=string; label= language2: name of the second language of this site
	language2 = Dansk
}

# Set the language of meta tag with DC.language to Danish, when in Danish
[globalVar = GP:L = 1]
plugin.meta.language = da
[end]

<INCLUDE_TYPOSCRIPT: source="FILE:typo3conf/settings/introduction.ts">
