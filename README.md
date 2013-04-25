#Test environment for TYPO3 EXT:workspace changes

## Installation

### Check configured ip address

Check if ip address in `Vagrantfile` is not in use. Default `192.168.156.40`.
Maybe use an other ip address.

	Vagrant::Config.run do |config|
	  config.vm.network :hostonly, '192.168.156.40'
	end

### Build virtual mashine**

	bundle install
	vagrant up

### Add ip address to host file**

Add domain `typo3-workspace-test-environment.dev` with defined ip address (Default `192.168.156.40`) to your host file.

### Import sql dump

	./snapshot.sh 20130424-1626

## Backend user

* admin : password