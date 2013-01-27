OpenPhoto / Installation for Fedora 16 + Apache
=======================
#### OpenPhoto, a photo service for the masses

## OS: Linux Fedora 16

This guide instructs you on how to install OpenPhoto on a Fedora server.

----------------------------------------

### Prerequisites

#### Database and File System Options

##### MySql 
You'll need to provide credentials for a MySql database. If the database doesn't already exist it will be created. If the user doesn't have `CREATE DATABASE` permissions then make sure to create the database.

##### AWS
If you're going to use AWS services then you'll need to be signed up for them.

* http://aws.amazon.com/simpledb/
* http://aws.amazon.com/s3/

#### Server Packages and Modules
Once you've confirmed that your cloud account is set up, you can get started on your server. For that you'll need to have _Apache_, _PHP_ and _curl_ installed with a few modules.

    yum groupinstall 'Development Tools'
    yum groupinstall 'Development Libraries'
    yum install httpd httpd-devel php php-devel php-common php-curl php-gd php-mcrypt pcre pcre-devel ImageMagick php-magickwand php-pecl-imagick ImageMagick-devel php-pear php-mysql
    pecl install apc
    echo "extension=apc.so" > /etc/php.d/apc.ini
    pecl install oauth
    echo "extension=oauth.so" > /etc/php.d/oauth.ini

----------------------------------------

### Installing OpenPhoto

Download and install the source code. We recommend `/var/www/yourdomain.com` but you can use any directory you'd like.

#### Using git clone

The _Apache 2_ user in Fedora 16 is `apache` so run the following commands to take ownership of the web files.

    yum install git
    git clone git://github.com/photo/frontend.git /var/www/yourdomain.com
    chown -R apache: /var/www/yourdomain.com

Assuming that this is a development machine you only need to make the config writable by the user Apache runs as. This user is likely `apache`.

    mkdir /var/www/yourdomain.com/src/userdata
    mkdir /var/www/yourdomain.com/src/html/photos
    mkdir /var/www/yourdomain.com/src/html/assets/cache
    chown apache: /var/www/yourdomain.com/src/userdata
    chown apache: /var/www/yourdomain.com/src/html/photos
    chown apache: /var/www/yourdomain.com/src/html/assets/cache

### Setting up Apache and PHP

#### Apache

You'll need to copy the sample virtual host configuration file from the source to `/etc/httpd/conf.d/`.

    cp /var/www/yourdomain.com/src/configs/openphoto-vhost.conf /etc/httpd/conf.d/openphoto.conf

You'll need to replace instances of `/path/to/openphoto/html/directory` with `/var/www/yourdomain.com/src/html` or wherever you placed the code.

Edit `/etc/httpd/conf/httpd.conf` and ensure the following modules are enabled: _rewrite_, _deflate_, _expires_, _headers_.  Here are the corresponding lines to the enabled apache modules in `httpd.conf`.

    LoadModule rewrite_module modules/mod_rewrite.so
    LoadModule deflate_module modules/mod_deflate.so
    LoadModule expires_module modules/mod_expires.so
    LoadModule headers_module modules/mod_headers.so

By default, any access to ini files is denied with a "Not Found" 404 HTTP code.  To enable a 404, or Forbidden return code, change the following lines in the virtual host file.

Uncomment:

    # 403 Forbidden for ini files
    #RewriteRule \.ini$ - [F,NC]

Comment:

    # 404 Not Found for ini files
    AliasMatch \.ini$	/404

### PHP

You should also verify that your `php.ini` file has a few important values set correctly.

    vi /etc/php.ini

Search for the following values and make sure they're correct.

    file_uploads = On
    upload_max_filesize = 16M
    post_max_size = 16M

Ensure that `/etc/php.d/apc.ini` and `/etc/php.d/oauth.ini` exist and that the php extensions are enabled.

Now you're ready to restart apache and visit the site in your browser.

    service httpd restart

### Launching your OpenPhoto site

Now you're ready to launch your OpenPhoto site. Point your browser to your host and you'll be taken to a setup screen. You'll need your cloud account credentials to continue.

Once you complete the 3 steps your site will be up and running and you'll be redirected there. The _setup_ screen won't show up anymore. If for any reason you want to go through the setup again you will need to delete the generated config file and refresh your browser.

    rm /var/www/yourdomain.com/src/userdata/configs/yourdomain.com.ini

**ENJOY!**
