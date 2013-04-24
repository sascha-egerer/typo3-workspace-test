include_recipe 'apt'
include_recipe 'apache2'
include_recipe 'mysql'
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
