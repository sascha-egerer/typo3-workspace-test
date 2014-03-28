# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::configure("2") do |config|
  # All Vagrant configuration is done here. The most common configuration
  # options are documented and commented below. For a complete reference,
  # please see the online documentation at vagrantup.com.

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box = 'precise64'

  # The url from where the 'config.vm.box' box will be fetched if it
  # doesn't already exist on the user's system.
  config.vm.box_url = 'http://files.vagrantup.com/precise64.box'

  # Assign this VM to a host-only network IP, allowing you to access it
  # via the IP. Host-only networks can talk to the host machine as well as
  # any other machines on the same network, but cannot be accessed (through this
  # network interface) by any external networks.
  config.vm.network 'private_network', ip: '192.168.156.40'

  # Assign this VM to a bridged network, allowing you to connect directly to a
  # network using the host's network device. This makes the VM appear as another
  # physical device on your network.
  # config.vm.network :bridged

  # Forward a port from the guest to the host, which allows for outside
  # computers to access the VM, whereas host only networking does not.
  # config.vm.forward_port 80, 8080

  # Share an additional folder to the guest VM. The first argument is
  # an identifier, the second is the path on the guest to mount the
  # folder, and the third is the path on the host to the actual folder.
  config.vm.synced_folder 'htdocs', '/var/www/workspace_test_environment/htdocs', :extra => 'dmode=777,fmode=666', :nfs => true

  config.vm.provision :chef_solo do |chef|
    # We're going to download our cookbooks from the web
    chef.cookbooks_path = 'config/vm/cookbooks'

    # Tell chef what recipe to run. In this case, the `vagrant_main` recipe
    # does all the magic.
    chef.add_recipe('vagrant_main')

    chef.json = {
      'php' => {
        'directives' => {
          'xdebug.remote_host' => '192.168.156.1'
        }
      }
    }

    #chef.json = {
    #  'mysql' => {
    #    'server_root_password' => 'iloverandompasswordsbutthiswilldo',
    #    'server_repl_password' => 'iloverandompasswordsbutthiswilldo',
    #    'server_debian_password' => 'iloverandompasswordsbutthiswilldo',
    #    'bind_address' => '127.0.0.1'
    #  },
    #  'php' => {
    #    'conf_dir' => '/etc/php5/apache2'
    #  }
    #}
  end

  config.vm.provider "virtualbox" do |v|

    # Boot with a GUI so you can see the screen. (Default is headless)
    # v.gui = true

    v.customize [
      'modifyvm', :id,
       '--memory', 1024,
       '--cpus', 1,
       '--name', 'typo3-workspace-test-environment',
       '--natdnsproxy1', 'off',
       '--natdnshostresolver1', 'on'
      ]
  end

end