config {
	# cat=config; type=boolean; label=Admin Panel: Turn on admin panel (mainly for testing purposes only)
	adminPanel = 0

	# cat=config; type=boolean; label=Debugging: Turn on debugging (testing purposes only)
	debug = 0

	# cat=config; type=string; label=Domain name for Base URL: (excluding slashes and protocol like http://)
	domain = ###HOSTNAME###

	# cat=config; type=string; label=Absolute URI prefix: (use "/" if running on top level; use empty value to use relative URI)
	absRefPrefix = ###PATH###

	# cat=config/enable; type=boolean; label=Enable RealURL (speaking URL path segments)
	tx_realurl_enable = ###ENABLE_REALURL###
}
