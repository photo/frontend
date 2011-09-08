OpenPhoto / Installation for OSX using Macports
=======================
#### OpenPhoto, a photo service for the masses

## OS: Mac OSX

This guide instructs you on how to install OpenPhoto on a Macintosh OSX computer.

----------------------------------------

### Prerequisites

#### Cloud Accounts

Before setting up your server you'll need to make sure you have your cloud accounts set up. If you're using Amazon then make sure you've enabled both S3 and SimpleDb.

* http://aws.amazon.com/simpledb/
* http://aws.amazon.com/s3/

#### Server Packages and Modules
Once you've confirmed that your cloud account is setup you can get started on your server. For that you'll need to have _Apache_, _PHP_ and _curl_ installed with a few modules.

This guide assumes you have [get it here][Macports installed]. If not you can . The easiest option is to use `.pkg` installer.

    port install apache2
    port install php5 +apache2
    port install php5-exif
    port install php5-curl
    port install php5-imagick
    port install php5-oauth
    port load apache2

----------------------------------------

### Installing OpenPhoto

Download and install the source code. We recommend `~/Sites/yourdomain.com` but you can use any directory you'd like.

#### Using git clone

    # install git if you don't have it already
    port install git-core
    git clone git@github.com:openphoto/frontend.git ~/Sites/yourdomain.com

#### Using tar

    cd ~/Sites
    wget https://github.com/openphoto/frontend/tarball/master -O openphoto.tar.gz
    tar -zxvf --group=www-data --owner=www-data openphoto.tar.gz
    mv openphoto-frontend-* yourdomain.com

Assuming that this is a development machine you can make the config writable by the user Apache runs as. Most likely `_www`.

    mkdir ~/Sites/yourdomain.com/src/configs/generated
    chown _www ~/Sites/yourdomain.com/src/configs/generated

----------------------------------------

### Setting up Apache and PHP

#### Apache

You'll need to make sure that you have named virtual hosts enabled in your Apache confs. First, copy the contents of `~/Sites/yourdomain.com/configs/openphoto-vhost.conf` onto your clipboard. Then open your `virtualhosts.conf` file.

    vi /opt/local/apache2/conf/extra/virtualhosts.conf

You can put the `NameVirtualHost` directive at the top of the file.

    NameVirtualHost *

Paste the contents of your clipboard into the bottom of the file and replace instances of `/path/to/openphoto/html/directory` with `/Users/yourusername/Sites/yourdomain.com/src/html` or wherever you placed the code. In the virtualhost conf make sure to specify the full path to your `Sites` directory.

### PHP

You should also verify that your `php.ini` file has a few important values set correctly.

    vi /opt/local/etc/php5/php.ini

Search for the following values and make sure they're correct.

    file_uploads = On
    upload_max_filesize = 16M
    post_max_size = 16M

Now you're ready to restart apache and visit the site in your browser.

    /opt/local/apache2/bin/apachectl restart

### Launching your OpenPhoto site

Now you're ready to launch your OpenPhoto site. Point your browser to your host and you'll be taken to a setup screen. You'll need your cloud account credentials to continue.

Once you complete the 3 steps your site will be up and running and you'll be redirected there. The _setup_ screen won't show up anymore. If for any reason you want to go through the setup again you will need to delete the generated config file and refresh your browser.

    rm /var/www/yourdomain.com/src/configs/generated/settings.ini

**ENJOY!**

[macports]: http://www.macports.org/install.php
