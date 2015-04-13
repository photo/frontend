OpenPhoto / Installation for Lighttpd on Ubuntu/Debian
=======================
#### OpenPhoto, a photo service for the masses

This guide instructs you on how to install OpenPhoto under Lighttpd on Ubuntu or Debian

----------------------------------------

### Prerequisites

#### Database and File System Options

##### MySql
You'll need to provide credentials for a MySql database. If the database doesn't already exist it will be created. If the user doesn't have `CREATE DATABASE` permissions then make sure it's already created.

##### AWS
If you're going to use AWS services then You'll need to be signed up for them.

* http://aws.amazon.com/simpledb/
* http://aws.amazon.com/s3/

#### Server Packages and Modules
Once you've confirmed that your database is setup you can get started on your server. For that you'll need to have _Lighttpd_, and _PHP_ installed with a few modules.

    apt-get update
    apt-get upgrade
    apt-get install lighttpd php5-cgi php5-curl php5-gd php5-mcrypt php-apc

And if you are going to use MySQL install `php5-mysql`.

There are also a few optional but recommended packages and modules.

    apt-get install php5-imagick exiftran

----------------------------------------

### Installing OpenPhoto

Download and install the source code. We recommend `/var/www/yourdomain.com` but you can use any directory you'd like.

#### Using git clone

    apt-get install git-core
    git clone git://github.com/photo/frontend.git /var/www/yourdomain.com

#### Using tar

    cd /var/www
    wget https://github.com/photo/frontend/tarball/master -O openphoto.tar.gz
    tar -zxvf openphoto.tar.gz
    mv openphoto-frontend-* yourdomain.com

There are certain directories that need to be created and made writable by the user the Lighttpd server runs as. Most likely (on Ubuntu/Debian) this is `www-data`.

    mkdir /var/www/yourdomain.com/src/userdata
    mkdir /var/www/yourdomain.com/src/html/photos
    mkdir /var/www/yourdomain.com/src/html/assets/cache
    chown www-data:www-data /var/www/yourdomain.com/src/userdata
    chown www-data:www-data /var/www/yourdomain.com/src/html/photos
    chown www-data:www-data /var/www/yourdomain.com/src/html/assets/cache

----------------------------------------

### Setting up Lighttpd and PHP

#### Lighttpd

First you will need to copy the sample Lighttpd configuration into a place where Lighty can find it.

    cp /var/www/yourdomain.com/src/configs/openphoto-lighttpd.conf /etc/lighttpd/conf-available/99-openphoto.conf

The `99` in the prefix of the destination filename above is so that Lighttpd loads our OpenPhoto configuration last. In reality you can use any number greater than the prefix of the FastCGI configuration.

Next open the configuration you just copied and edit it to match your site. Edit the `$HTTP["host"]` line and replace the variable contents with the subdomain where you are installing OpenPhoto. The value is a regular expression, so keep the `\.` which matches a period instead of any character as well as the `^` `$` to direct matching the beginning and end of the hostname respectively.

Now replace the path on the `server.document-root` line with the path to the `src/html` sub-directory where you downloaded/copied OpenPhoto. This path would be `/var/www/yourdomain.com/src/html` if you followed the directions in the last section exactly.

A few modules must be enabled for use by Lighty for our OpenPhoto install. First edit `/etc/lighttpd/lighttpd.conf` and uncomment the line for `mod_rewrite` under the `server.modules` section. Next enable PHP through the FastCGI module from the command line.

    lighttpd-enable-mod fastcgi fastcgi-php

Finally, enable your edited configuration and force Lighty to reload its configuration.

    lighttpd-enable-mod openphoto
    /etc/init.d/lighttpd force-reload

### PHP

You should also verify that your `php.ini` file has a few important values set correctly.

    vi /etc/php5/cgi/php.ini

Search for the following values and make sure they're correct.

    file_uploads = On
    upload_max_filesize = 16M
    post_max_size = 16M

Verify that the mcrypt module is enabled

    php5enmod mcrypt

If you made any changes then restart your Lighttpd server.

    /etc/init.d/lighttpd restart

### Launching your OpenPhoto site

Now you're ready to launch your OpenPhoto site. Point your browser to your host and you'll be taken to a setup screen. You'll need your cloud account credentials to continue.

Once you complete the 3 steps your site will be up and running and you'll be redirected there. The _setup_ screen won't show up anymore. If for any reason you want to go through the setup again you will need to delete the generated config file and refresh your browser.

    rm /var/www/yourdomain.com/src/userdata/configs/yourdomain.com.ini

**ENJOY!**
