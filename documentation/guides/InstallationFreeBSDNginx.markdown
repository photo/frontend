OpenPhoto / Installation for FreeBSD + Nginx
=======================
#### OpenPhoto, a photo service for the masses

## OS: FreeBSD 9.0+

This guide instructs you on how to install OpenPhoto on an FreeBSD Server

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


##### Install Nginx

Compile Nginx with :
HTTP_MODULE
HTTP_CACHE_MODULE
HTTP_GZIP_STATIC_MODULE
HTTP_REWRITE_MODULE
HTTP_UPLOAD_MODULE
HTTP_UPLOAD_PROGRESS

    cd /usr/ports/www/nginx
    make config install clean distclean

##### Install php5

Compile php5 with :
CLI
FPM
SUHOSIN
MULTIBYTE
MAILHEAD

    cd /usr/port/lang/php5
    make config install clean distclean

##### Install php5 extentions

Compile php5 extentions with :
BZ2
CALENDAR
CTYPE
CURL
DOM
FILEINFO
FILTER
GD
HASH
ICONV
JSON
MBSTRING
MCRYPT
OPENSSL
PDF
PHAR
POSIX
SESSION
SIMPLEXML
TOKENIZER
XML
XMLREADER
XMLWRITER
XSL
ZLIB

    cd /usr/port/lang/php5-extentions
    make config install clean distclean



And if you are going to use MySQL compile `php5-extentions` with `MYSQL MYSQLI`.

There are also a few optional but recommended packages and modules.

    /usr/ports/net/pecl-oauth
    /usr/ports/graphics/pecl-imagick
    /usr/ports/graphics/exiftran

----------------------------------------

### Installing OpenPhoto

Download and install the source code. We recommend `/usr/local/www/yourdomain.com` but you can use any directory you'd like.

#### Using git clone

    pkg_add -r git-core
    git clone git://github.com/photo/frontend.git /usr/local/www/yourdomain.com

#### Using tar

    cd /usr/local/www
    wget https://github.com/photo/frontend/tarball/master -O openphoto.tar.gz
    tar -zxvf openphoto.tar.gz
    mv openphoto-frontend-* yourdomain.com

Assuming that this is a development machine you only need to make the config writable by the user Apache runs as. Most likely `www`.

    mkdir /usr/local/www/yourdomain.com/src/userdata
    mkdir /usr/local/www/yourdomain.com/src/html/photos
    mkdir /usr/local/www/yourdomain.com/src/html/assets/cache
    chown www:www /usr/local/www/yourdomain.com/src/userdata
    chown www:www /usr/local/www/yourdomain.com/src/html/photos
    chown www:www /usr/local/www/yourdomain.com/src/html/assets/cache

----------------------------------------

### Setting up NGinx and PHP

#### NGinx

You'll need to copy the sample virtual host configuration file from the source to `/etc/nginx/sites-enabled`.

    cp /usr/local/www/yourdomain.com/src/configs/openphoto-nginx.conf /usr/local/etc/nginx/sites-enabled/openphoto

You'll need to replace:
    * The host name (yourdomain.com)
    * The path where OpenPhoto is installed (/usr/local/www/yourdomain.com/src/html/)

    /usr/local/etc/nginx/sites-enabled/openphoto

### PHP

You should also verify that your `php.ini` file has a few important values set correctly.

    vi /usr/local/etc/php.ini

Search for the following values and make sure they're correct.

    file_uploads = On
    upload_max_filesize = 16M
    post_max_size = 16M

Now you're ready to restart apache and visit the site in your browser.

    /usr/local/etc/rc.d/php-fpm restart
    /usr/local/etc/rc.d/nginx restart

### Launching your OpenPhoto site

Now you're ready to launch your OpenPhoto site. Point your browser to your host and you'll be taken to a setup screen. You'll need your cloud account credentials to continue.

Once you complete the 3 steps your site will be up and running and you'll be redirected there. The _setup_ screen won't show up anymore. If for any reason you want to go through the setup again you will need to delete the generated config file and refresh your browser.

    rm /usr/local/www/yourdomain.com/src/userdata/configs/yourdomain.com.ini

**ENJOY!**
