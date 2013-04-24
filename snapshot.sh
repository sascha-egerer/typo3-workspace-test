#!/bin/bash

SSH_USER='vagrant'
SSH_HOST='typo3-workspace-test-environment.dev'
VHOST_PATH='/var/www/workspace_test_environment/htdocs'

# init DB access
initDb() {
	DATABASE='typo3-workspace-test-environment'
	DBUSER='root'
	DBPASSWORD='iloverandompasswordsbutthiswilldo'
}


# dump DB data
dumpDb() {
	ssh ${SSH_USER}@${SSH_HOST} "mysqldump -u${DBUSER} -p${DBPASSWORD} -h localhost --opt --default-character-set=utf8 ${DATABASE} > ${VHOST_PATH}/dump.sql"
}

# restore DB
installDb() {
	ssh ${SSH_USER}@${SSH_HOST} "mysql -u${DBUSER} -p${DBPASSWORD} -h localhost ${DATABASE} < ${VHOST_PATH}/dump.sql"
}

# tar file and DB data
# move tarball away
buildTarball() {
	tar --create --gzip --preserve-permissions --check-links -f config/data/${SNAPSHOT} htdocs/dump.sql
}

extractTarball() {
	echo "Snapshot will overwrite existing data ..."
	sleep 5
	# make backup
	dumpDb
	tar --extract --gzip --preserve-permissions -f config/data/${SNAPSHOT}
}


# check existence of snapshot file
checkSnapshot() {
	echo ${SNAPSHOT}
	if [ -f config/data/${SNAPSHOT} ]; then
		return;
	fi
	
	echo "Available snapshot files: (use timestamp as parameter)"
	ls config/data/
	exit
}


# remove temporary files
cleanup() {
	rm htdocs/dump.sql
}


# execute snapshot
snapshot() {
	dumpDb
	buildTarball
}

# execute restore
restore () {
	checkSnapshot
	extractTarball
	installDb
}



initDb
case $# in
	0)
		SNAPSHOT="typo3-workspace-test-environment.`date +%Y%m%d-%H%M`.tgz"
		snapshot
		cleanup
	;;

	1)
		SNAPSHOT="typo3-workspace-test-environment.$1.tgz"
		restore
		cleanup
	;;
	
	*)
		echo "Use this script to create and restore snapshots of the TYPO3 installation in config/data/."
		echo "No parameter creates a snapshot."
		echo "A timestamp from directory 'config/data/' as single parameter restores that snapshot."
	;;
esac