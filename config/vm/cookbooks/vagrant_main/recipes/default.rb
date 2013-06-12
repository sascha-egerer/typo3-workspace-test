node.override['mysql']['server_root_password'] = 'iloverandompasswordsbutthiswilldo'
node.override['mysql']['server_repl_password'] = 'iloverandompasswordsbutthiswilldo'
node.override['mysql']['server_debian_password'] = 'iloverandompasswordsbutthiswilldo'
node.override['mysql']['bind_address'] = '127.0.0.1'
node.override['php']['conf_dir'] = '/etc/php5/apache2'

include_recipe 'apt'
include_recipe 'vim'
include_recipe 'apache2'
include_recipe 'apache2::mod_php5'
include_recipe 'php'
include_recipe 'php::module_mysql'
include_recipe 'php::module_curl'
include_recipe 'php::module_gd'
include_recipe 'mysql'
include_recipe 'mysql::server'
include_recipe 'database'
include_recipe 'database::mysql'

execute 'disable-default-site' do
  command 'sudo a2dissite default'
  notifies :reload, resources(:service => 'apache2'), :delayed
end

directory '/var/www/workspace_test_environment/htdocs' do
  owner 'vagrant'
  group 'www-data'
  mode '0755'
  action :create
end

mysql_database "Create database 'typo3-workspace-test-environment'" do
  connection ({:host => 'localhost', :username => 'root', :password => node['mysql']['server_root_password']})
  database_name 'typo3-workspace-test-environment'
  action :create
end

web_app 'project' do
  template 'web_app.conf.erb'
  docroot '/var/www/workspace_test_environment/htdocs'
  allow_override 'All'
  directory_index 'index.php index.html'
  server_name 'typo3-workspace-test-environment.dev'
  server_aliases []
  notifies :reload, resources(:service => 'apache2'), :delayed
end

package "graphicsmagick"
