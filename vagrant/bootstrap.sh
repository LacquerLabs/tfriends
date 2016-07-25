#!/usr/bin/env bash

# Use single quotes instead of double quotes to make it work with special-character passwords
PASSWORD='password'
PROJECTFOLDER='/vagrant_data/transfriendly_app/public'

# create project folder
# sudo mkdir "${PROJECTFOLDER}"

# Load up the release information
. /etc/lsb-release

# Do the initial apt-get update
echo "Initial apt-get update..."
sudo apt-get update

echo "Installing 'apt-spy2'. This tool lets us autoconfigure your 'apt' sources.list to a nearby location."
echo "  This may take a while..."

# Ensure dependencies are installed (These are needed to dynamically determine your country code).
# * ruby >= 2.0 is needed for apt-spy2
# * zlib1g-dev (zlib) is needed to build apt-spy2 from "native extensions" (needed to install "nokogiri" prerequisite)
# * dnsutils ensures 'dig' is installed (to get IP address)
# * geoip-bin ensures 'geoiplookup' is installed (lets us look up country code via IP)
sudo apt-get install -y ruby2.0 zlib1g-dev dnsutils geoip-bin
sudo mv /usr/bin/ruby /usr/bin/ruby.old
sudo ln -s /usr/bin/ruby2.0 /usr/bin/ruby

# Install/Update RubyGems for the provider
echo "Installing RubyGems..."
if [ $DISTRIB_CODENAME != "trusty" ]; then
  sudo apt-get install -y rubygems
fi
gem install --no-ri --no-rdoc rubygems-update
update_rubygems

# Figure out the two-letter country code for the current locale, based on IP address
# First, let's get our public IP address via OpenDNS (e.g. http://unix.stackexchange.com/a/81699)
CURRENTIP=`dig +short myip.opendns.com @resolver1.opendns.com`

# Next, let's lookup our country code via IP address
COUNTRY=`geoiplookup $CURRENTIP | awk -F: '{ print $2 }' | awk -F, '{ print $1}' | tr -d "[:space:]"`

#If country code is empty or != 2 characters, then use "US" as a default
if [ -z "$COUNTRY" ] || [ "${#COUNTRY}" -ne "2" ]; then
   COUNTRY="US"
fi

if [ "$(gem search -i apt-spy2)" = "false" ]; then
  echo "Installing apt-spy2 (and prerequisites)..."
  gem install --no-ri --no-rdoc apt-spy2
  echo "... apt-spy2 installed!"
fi

echo "... Setting 'apt' sources.list for closest mirror to country=$COUNTRY"
sudo apt-spy2 check
# By default lookup a mirror using launchpad.net
sudo apt-spy2 fix --launchpad --commit --country=$COUNTRY

# apt-spy2 requires running an 'apt-get update' after doing a 'fix'
echo "Re-running apt-get update after sources updated..."
set +e  #temporarily ignore errors
sudo apt-get update
RESULT=$?
set -e  # reenable exit on error

# If previous apt-get errored out, re-run apt-spy2 with ubuntu list of mirrors (i.e. not launchpad)
if [ $RESULT -ne 0 ]; then
  echo "Initial apt-get update failed. Trying a different mirror as a fallback..."
  sudo apt-spy2 fix --commit --country=$COUNTRY
  echo "Re-running apt-get update after sources updated (again)..."
  sudo apt-get update
fi

# update / upgrade
# sudo apt-get update >/dev/null
sudo apt-get -y upgrade

# install apache 2.5 and php 5.5
sudo apt-get install -y apache2
sudo apt-get install -y php5

# install mysql and give password to installer
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $PASSWORD"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $PASSWORD"
sudo apt-get -y install mysql-server
sudo apt-get install php5-mysql

# install phpmyadmin and give password(s) to installer
# for simplicity I'm using the same password for mysql and phpmyadmin
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2"
sudo apt-get -y install phpmyadmin

# setup hosts file
VHOST=$(cat <<EOF
<VirtualHost *:80>
    DocumentRoot "${PROJECTFOLDER}"
    <Directory "${PROJECTFOLDER}">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF
)
echo "${VHOST}" > /etc/apache2/sites-available/000-default.conf

# enable mod_rewrite
sudo a2enmod rewrite

# restart apache
service apache2 restart

# install git
sudo apt-get -y install git

# install Composer
curl -s https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer


