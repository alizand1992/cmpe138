#!/bin/bash
printf "sudo password: "
read -s password
echo $password | sudo -S apt-get update
echo $password | sudo -S apt-get upgrade -y
echo $password | sudo -S apt-get install -y apache2 mysql-server php7.2 libapache2-mod-php7.2

project_dir=$(echo $PWD | rev | cut -c11-100 | rev)

sudo bash -c "cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:80>
	# The ServerName directive sets the request scheme, hostname and port that
	# the server uses to identify itself. This is used when creating
	# redirection URLs. In the context of virtual hosts, the ServerName
	# specifies what hostname must appear in the request's Host: header to
	# match this virtual host. For the default virtual host (this file) this
	# value is not decisive as it is used as a last resort host regardless.
	# However, you must set it for any further virtual host explicitly.
	# ServerName www.example.com
    # This has been modified by bash

	ServerAdmin webmaster@localhost
	DocumentRoot $project_dir

    <Directory $project_dir>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
    </Directory>

	# Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
	# error, crit, alert, emerg.
	# It is also possible to configure the loglevel for particular
	# modules, e.g.
	#LogLevel info ssl:warn

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined

	# For most configuration files from conf-available/, which are
	# enabled or disabled at a global level, it is possible to
	# include a line for only one particular virtual host. For example the
	# following line enables the CGI configuration for this host only
	# after it has been globally disabled with \"a2disconf\".
	#Include conf-available/serve-cgi-bin.conf
</VirtualHost>

# vim: syntax=apache ts=4 sw=4 sts=4 sr noet

EOF"

echo $password | sudo -S a2ensite 000-default.conf
echo $password | sudo -S a2enmod php7.2
echo $password | sudo -S service apache2 restart

cat <<EOF

The site should be running on localhost:80

EOF
