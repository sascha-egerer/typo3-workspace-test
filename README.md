#Test environment for TYPO3 EXT:workspaces changes

## Download

### Git submodule

The workspaces testing extension itself is managed in a different repository (http://github.com:dkd/workspace_test)

	git submodule update --init

## VM Installation

### Requirements

TL;DR: Ruby + Bundler + rvm (optional)

Since we want to set up the virtual machine with [Vagrant](http://www.vagrantup.com/) a working Ruby environment is crucial.

We use [Bundler](http://gembundler.com/) to install the required Gems. They're configured in the `Gemfile` and can be
installed manually - if you absolutely want to do that.

We're using [rvm](https://rvm.io/) and rely on the `.rvmrc` file which sets `vagrant` as an alias for `bundle exec vagrant`.
Without rvm we suggest to set the alias in another way so you can type `vagrant <command>` instead of `bundle exec vagrant <command>`.
More important than the alias is the possibility to set the exact Ruby version and patch level.

rvm and Bundler make it easy to set an environment for all users which is known to work.

### Check configured IP address

Check if IP address in `Vagrantfile` is not in use. Default `192.168.156.40`.
Maybe use another IP address.

	Vagrant::Config.run do |config|
	  config.vm.network :hostonly, '192.168.156.40'
	end

### Build virtual machine

	bundle install
	vagrant up


### Add IP address to hosts file

Add domain `typo3-workspace-test-environment.dev` with defined IP address (Default `192.168.156.40`) to your host file.

### Import SSH key

Download and install the private SSH key from Vagrant's Github
repository so you can `ssh` to your VM.

  curl -s https://raw.github.com/mitchellh/vagrant/master/keys/vagrant -o ~/.ssh/vagrant
  chmod 600 ~/.ssh/vagrant


## TYPO3 Installation

### TYPO3 sources

The TYPO3 source have to be put into the htdocs folder. Using a symlink on the host machine doesn't work so there are
two ways to achieve this:

* clone the TYPO3 core as a submodule
	git submodule add -- git://git.typo3.org/Packages/TYPO3.CMS.git htdocs/typo3_src
* mount an existing clone via `Vagrantfile` and symlink the clone from htdocs
The symlink has to be relative to the guest file system!

### Snapshot script

There's a small shell script `snapshot.sh` used to dump and reinstall the data (DB + files) of the TYPO3 installation.
It's primary use is to reset the installation for every test run.

 Usage:

 Without parameter the script creates a new snapshot in directory `config/data/` with a timestamp as part of the
 filename. To restore a distinct snapshot just add the timestamp part of the snapshot's filename as parameter:

	./snapshot.sh demo-content

### Backend user

* admin : password