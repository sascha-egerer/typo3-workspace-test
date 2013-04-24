/*
lib.footer fetches the content record in 'Generated content > Footer content'
and displays that on all pages.
*/

# The method used here to fetch a record from a page or folder is slightly different from the one used
# to fetch the loginbox in lib.contentleft
# The method here doesn't refer to a specific record but takes the first record from the folder. This
# has the advantage that an editor can delete the record and just create a new one, and it still works.
/*
	lib.footer = CONTENT
	lib.footer {
		table = tt_content
		select {
			orderBy = sorting
			max = 1
			pidInList = {$contentpage.footerPID}
		}
	}
*/

lib.footer = COA

lib.footer.10 < styles.content.getLeft
lib.footer.10.select.pidInList = {$contentpage.footerPID}
lib.footer.10.stdWrap.wrap = <div class="footerFrame">|</div>

lib.footer.20 < styles.content.get
lib.footer.20.select.pidInList = {$contentpage.footerPID}
lib.footer.20.stdWrap.wrap = <div class="footerFrame">|</div>

lib.footer.30 < styles.content.getRight
lib.footer.30.select.pidInList = {$contentpage.footerPID}
lib.footer.30.stdWrap.wrap = <div class="footerFrame-last">|</div>
