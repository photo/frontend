#!/bin/bash
#######################################
# OpenPhoto Install
# Run with sudo for best results
#######################################
SECONDS=0
if [[ "$(/usr/bin/whoami)" != "root" ]]; then 
    echo "This script must be run as root or using sudo.Script aborted."
    exit 1
fi

echo ""
echo ""
echo "===================================================="
echo "Updating Ubuntu and apt-get"
echo "===================================================="
echo ""
echo ""

apt-get update --assume-yes --quiet
apt-get upgrade --assume-yes --quiet

echo ""
echo ""
echo "===================================================="
echo "Installing needed packages and modules"
echo "===================================================="
echo ""
echo ""

apt-get install --assume-yes --quiet apache2 php5 libapache2-mod-php5 php5-curl
a2enmod rewrite

echo ""
echo ""
echo "===================================================="
echo "Installing optional but recommended packages and modules"
echo "===================================================="
echo ""
echo ""

apt-get install --assume-yes --quiet php5-imagick exiftran
a2enmod deflate
a2enmod expires
a2enmod headers

echo ""
echo ""
echo "===================================================="
echo "Downloading OpenPhoto and unpacking"
echo "===================================================="
echo ""
echo ""

wget https://github.com/openphoto/frontend/tarball/master -O openphoto.tar.gz
tar -zxvf openphoto.tar.gz > /dev/null 2>&1
mv openphoto-frontend-* /var/www/openphoto
sudo rm openphoto.tar.gz

echo ""
echo ""
echo "===================================================="
echo "Setting permissions for Dev server"
echo "===================================================="
echo ""
echo ""

mkdir /var/www/openphoto/src/configs/generated
chown www-data:www-data /var/www/openphoto/src/configs/generated

echo ""
echo ""
echo "===================================================="
echo "Setting up Apache"
echo "===================================================="
echo ""
echo ""

cp /var/www/openphoto/src/configs/openphoto-vhost.conf /etc/apache2/sites-available/openphoto
sed -e 's/<VirtualHost \*>/<VirtualHost \*:80>/g' -e 's/\/path\/to\/openphoto\/html\/directory/\/var\/www\/openphoto\/src\/html/g' /var/www/openphoto/src/configs/openphoto-vhost.conf > /etc/apache2/sites-available/openphoto
a2dissite default
a2ensite openphoto

echo ""
echo ""
echo "===================================================="
echo "Adjusting PHP settings"
echo "===================================================="
echo ""
echo ""

sed -e 's/file_uploads.*/file_uploads = On/g' -e 's/upload_max_filesize.*/upload_max_filesize = 16M/g' -e 's/post_max_size.*/post_max_size = 16M/g' /etc/php5/apache2/php.ini > /etc/php5/apache2/php.ini.tmp
mv /etc/php5/apache2/php.ini.tmp /etc/php5/apache2/php.ini

echo ""
echo ""
echo "===================================================="
echo "Launching Your OpenPhoto site"
echo "===================================================="
echo ""
echo ""

/etc/init.d/apache2 restart

echo ""
echo ""
echo ""
echo "****************************************************"
echo "===================================================="
echo "CONGRATULATIONS!!!"
echo ""
echo "The photographic heavens are applauding your"
echo "brand new installation of OpenPhoto."
echo ""
echo "You should now be able to view your shiny new"
echo "installation at: "
echo ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'
echo ""
echo "Took $SECONDS seconds to install."
echo "===================================================="
echo "****************************************************"
echo ""
echo ""
echo ""
echo ""
echo ""
echo ""












