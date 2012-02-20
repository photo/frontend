OpenPhoto / Installation for Lighttpd on Ubuntu/Debian
=======================
#### OpenPhoto, a photo service for the masses

This guide instructs you on how to install OpenPhoto under Lighttpd on Ubuntu or Debian

----------------------------------------

### Prerequisites

#### Database and File System Options

##### MySql 
You'll need to provide credentials for a MySql database. If the database doesn't already exist it will be created. If the user doesn't have `CREATE DATABASE` permissions then make sure it's already created.

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
    git clone git@github.com:openphoto/frontend.git /var/www/yourdomain.com
    chown -R www-data:www-data /var/www/yourdomain.com

#### Using tar

    cd /var/www
    wget https://github.com/openphoto/frontend/tarball/master -O openphoto.tar.gz
    tar -zxvf openphoto.tar.gz
    mv openphoto-frontend-* yourdomain.com
    chown -R www-data:www-data yourdomain.com

Assuming that this is a development machine you only need to make the config writable by the user Lighttpd runs as. Most likely `www-data`.

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

The 99 in the prefix of the destination filename in the copy command above is so that Lighttpd loads our OpenPhoto configuration last. In reality you can use any number greater than the prefix of the CGI configuration.

Next open the configuration you just copied and edit it to match your site. Edit the `$HTTP["host"]` line and replace with the subdomain where you are installing OpenPhoto. The value inside the quotes on this line is a regular expression, so keep the `\.` which match a period instead of any character and the `^` `$` to direct matching the beginning and end of the hostname respectively. Now replace the path on the `server.document-root` line with the path to the `src` sub-directory of where you downloaded/copied OpenPhoto, this would be `/var/www/yourdomain.com/src` if you followed the directions in the last section exactly.

We must enable the modules we will be using with our OpenPhoto install. First edit `/etc/lighttpd/lighttpd.conf` and uncomment the line for `mod_rewrite` under the `server.modules` section. Next enable PHP through the FastCGI module from the command line.

    lighttpd-enable-mod fastcgi

Finally, enable your configuration.

    lighttpd-enable-mod openphoto
    /etc/init.d/lighttpd force-reload

### PHP

You should also verify that your `php.ini` file has a few important values set correctly.

    vi /etc/php5/cgi/php.ini

Search for the following values and make sure they're correct.

    file_uploads = On
    upload_max_filesize = 16M
    post_max_size = 16M

If you made any changes then restart your server.

    /etc/init.d/lighttpd restart

### Launching your OpenPhoto site

Now you're ready to launch your OpenPhoto site. Point your browser to your host and you'll be taken to a setup screen. You'll need your cloud account credentials to continue.

Once you complete the 3 steps your site will be up and running and you'll be redirected there. The _setup_ screen won't show up anymore. If for any reason you want to go through the setup again you will need to delete the generated config file and refresh your browser.

    rm /var/www/yourdomain.com/src/configs/generated/settings.ini

**ENJOY!**

