OpenPhoto / Installation for Ubuntu|Debian + Nginx
=======================
#### OpenPhoto, a photo service for the masses

## OS: Linux Ubuntu 12.04+ or Debian 6.0+

This guide instructs you on how to install OpenPhoto on an Ubuntu Desktop|Server or Debian distribution.

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
Once you've confirmed that your cloud account is setup you can get started on your server. For that you'll need to have _NGinx_, _PHP-FPM_ and _CURL_ installed with a few modules.

    apt-get update
    apt-get upgrade
    apt-get install nginx php5-fpm curl php5-curl php5-gd php5-mcrypt php-pear

And if you are going to use MySQL install `php5-mysql`.

There are also a few optional but recommended packages and modules.

    apt-get install php5-dev php5-imagick exiftran
    pecl install oauth
    sh -c "echo \"extension=oauth.so\" >> /etc/php5/conf.d/oauth.ini"

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

Assuming that this is a development machine you only need to make the config writable by the user Nginx runs as. Most likely `www-data`.

    mkdir /var/www/yourdomain.com/src/userdata
    mkdir /var/www/yourdomain.com/src/html/photos
    mkdir /var/www/yourdomain.com/src/html/assets/cache
    chown www-data:www-data /var/www/yourdomain.com/src/userdata
    chown www-data:www-data /var/www/yourdomain.com/src/html/photos
    chown www-data:www-data /var/www/yourdomain.com/src/html/assets/cache

----------------------------------------

### Setting up NGinx and PHP

#### NGinx

You'll need to copy the sample virtual host configuration file from the source to `/etc/nginx/sites-enabled`.

    cp /var/www/yourdomain.com/src/configs/openphoto-nginx.conf /etc/nginx/sites-enabled/openphoto

You'll need to replace:

* The host name (yourdomain.com)
* The path where OpenPhoto is installed (/var/www/yourdomain.com/src/html/) 

    `/etc/nginx/sites-enabled/openphoto`

You also need to change the port on which nginx talks to PHP. Change the
comments in the fastcgi_pass section to look like this:

    #fastcgi_pass 127.0.0.1:9000;
    # Unix socket is faster and default on Ubuntu/Debian:
    fastcgi_pass fastcgi_pass unix:/var/run/php5-fpm.sock;

### PHP

You should also verify that your `php.ini` file has a few important values set correctly.

    vi etc/php5/fpm/php.ini

Search for the following values and make sure they're correct.

    file_uploads = On
    upload_max_filesize = 16M
    post_max_size = 16M

Now you're ready to restart NGinx and visit the site in your browser.

    service php-fpm restart
    service nginx restart

### Launching your OpenPhoto site

Now you're ready to launch your OpenPhoto site. Point your browser to your host and you'll be taken to a setup screen. You'll need your cloud account credentials to continue.

Once you complete the 3 steps your site will be up and running and you'll be redirected there. The _setup_ screen won't show up anymore. If for any reason you want to go through the setup again you will need to delete the generated config file and refresh your browser.

    rm /var/www/yourdomain.com/src/userdata/configs/yourdomain.com.ini

**ENJOY!**
