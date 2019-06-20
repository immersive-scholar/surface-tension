# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure("2") do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # install plugin for sshfs
  vagrant_plugins = %w(vagrant-sshfs)
  vagrant_plugins.each do |plugin|
    unless Vagrant.has_plugin? plugin
      puts "Plugin #{plugin} is not installed. Install it with:"
      puts "vagrant plugin install #{vagrant_plugins.join(' ')}"
      exit
    end
  end

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://vagrantcloud.com/search.
  config.vm.box = "centos/7"

  # use sshfs for synced port
  config.vm.synced_folder ".", "/vagrant", type: "sshfs"


  # main provisioner
  config.vm.provision "ansible_local" do |ansible|
    ansible.galaxy_role_file = 'ansible/requirements.yml'
    ansible.playbook = 'ansible/playbook.yml'
    ansible.inventory_path = 'ansible/inventories/development.ini'
    ansible.limit = 'all'
  end

  # forwarded ports
  config.vm.network "forwarded_port", guest: 80, host: 8080, auto_correct: true

end
