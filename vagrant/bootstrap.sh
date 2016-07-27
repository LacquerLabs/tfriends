#!/usr/bin/env bash

# Use single quotes instead of double quotes to make it work with special-character passwords
PASSWORD='password'
PROJECTFOLDER='/vagrant_data/transfriendly_app/public'
DOMAIN='transfriendly.vm'

# create project folder
# sudo mkdir "${PROJECTFOLDER}"

# Load up the release information
. /etc/lsb-release

# Do the initial apt-get update
echo "Initial apt-get update..."
sudo apt-get update
sudo apt-get -y upgrade

# install apache 2.5 and php 5.5
sudo apt-get install -y apache2
sudo apt-get install -y php5

# install mysql and give password to installer
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $PASSWORD"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $PASSWORD"
sudo apt-get -y install mysql-server
sudo apt-get install php5-mysql
sudo apt-get -y install git

sudo php5enmod mcrypt
sudo php5enmod gd

# install phpmyadmin and give password(s) to installer
# for simplicity I'm using the same password for mysql and phpmyadmin
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2"
sudo apt-get -y install phpmyadmin

sudo mkdir /etc/apache2/ssl/
sudo openssl genrsa -out "/etc/apache2/ssl/${DOMAIN}.key" 2048
sudo openssl req -new -x509 -key "/etc/apache2/ssl/${DOMAIN}.key" -out "/etc/apache2/ssl/${DOMAIN}.crt"  -days 3650 -subj /CN="${DOMAIN}"

# setup hosts file
VHOST=$(cat <<EOF
<VirtualHost *:80>
    DocumentRoot "${PROJECTFOLDER}"
    Redirect permanent / https://${DOMAIN}/
</VirtualHost>
<VirtualHost *:443>
    DocumentRoot "${PROJECTFOLDER}"
    <Directory "${PROJECTFOLDER}">
        AllowOverride All
        Require all granted
    </Directory>
    SSLEngine on
    SSLCertificateFile /etc/apache2/ssl/${DOMAIN}.crt
    SSLCertificateKeyFile /etc/apache2/ssl/${DOMAIN}.key
</VirtualHost>
EOF
)
echo "${VHOST}" > /etc/apache2/sites-available/000-default.conf

# enable mod_rewrite
sudo a2enmod rewrite
sudo a2enmod ssl

# restart apache
service apache2 restart

# install Composer
# curl -s https://getcomposer.org/installer | php
# mv composer.phar /usr/local/bin/composer

sudo mysql -u root --password=password < /vagrant_data/db_structure.sql

sudo apt-get autoclean -y
sudo apt-get autoremove -y
