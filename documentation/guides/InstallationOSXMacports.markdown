OpenPhoto / Installation for OSX using Macports
=======================
#### OpenPhoto, a photo service for the masses

## OS: Mac OSX

This guide instructs you on how to install OpenPhoto on a Macintosh OSX computer.

----------------------------------------

### Prerequisites

#### Database and File System Options

##### MySql 
You'll need to provide credentials for a MySql database. If the database doesn't already exist it will be created. If the user doesn't have `CREATE DATABASE` permissions then make sure it's already created.

##### AWS
If you're going to use AWS services then you'll need to be signed up for them.

* http://aws.amazon.com/simpledb/
* http://aws.amazon.com/s3/

#### Server Packages and Modules
Once you've confirmed that your cloud account is setup you can get started on your server. For that you'll need to have _Apache_, _PHP_ and _curl_ installed with a few modules.

This guide assumes you have Macports installed. If not you can [get it here](http://www.macports.org/install.php). The easiest option is to use `.pkg` installer.

    sudo port install apache2
    sudo port install php5 +apache2
    
    cd /opt/local/apache2/modules
    sudo /opt/local/apache2/bin/apxs -a -e -n "php5" libphp5.so
    
    sudo port install php5-exif
    sudo port install php5-curl
    sudo port install php5-imagick
    sudo port install php5-oauth
    sudo port install php5-mcrypt
    sudo port install php5-apc
    sudo port load apache2

----------------------------------------

### Installing OpenPhoto

Download and install the source code. We recommend `~/Sites/yourdomain.com` but you can use any directory you'd like.

#### Using git clone

    # OSX should have git already installed. If not:
    sudo port install git-core
    git clone git://github.com/photo/frontend.git ~/Sites/yourdomain.com

#### Using tar

    cd ~/Sites
    wget https://github.com/photo/frontend/tarball/master -O openphoto.tar.gz
    tar -zxvf openphoto.tar.gz
    mv openphoto-frontend-* yourdomain.com

Assuming that this is a development machine you can make the config writable by the user Apache runs as. Most likely `_www`.

    cd ~/Sites/yourdomain.com
    mkdir src/userdata
    chown _www src/userdata

----------------------------------------

### Setting up Apache and PHP

#### Apache

You'll need to make sure that you have named virtual hosts enabled in your Apache confs. 

    sudo nano /opt/local/apache2/conf/httpd.conf
    
Enable virtual hosts:

    # Virtual hosts                                                                     
    Include conf/extra/httpd-vhosts.conf  

Ensure the PHP module is loaded (various places in `httpd.conf`):

    LoadModule php5_module        modules/libphp5.so
    
    DirectoryIndex index.html index.php
    
    Include conf/extra/mod_php.conf

Copy the contents of `~/Sites/yourdomain.com/src/configs/openphoto-vhost.conf` onto your clipboard. Then open your `httpd-vhosts.conf` file.

    sudo nano /opt/local/apache2/conf/extra/httpd-vhosts.conf

You can put the `NameVirtualHost` directive at the top of the file.

    NameVirtualHost *

Paste the contents of your clipboard into the bottom of the file and replace instances of `/path/to/openphoto/html/directory` with `/Users/yourusername/Sites/yourdomain.com/src/html` or wherever you placed the code. In the virtualhost conf make sure to specify the full path to your `Sites` directory.

By default, any access to ini files is denied with a "Not Found" 404 HTTP code.  To enable a 404, or Forbidden return code, change the following lines in the virtual host file.

Uncomment:

    # 403 Forbidden for ini files
    #RewriteRule \.ini$ - [F,NC]

Comment:

    # 404 Not Found for ini files
    AliasMatch \.ini$	/404


### PHP

You should also verify that your `php.ini` file has a few important values set correctly.

    sudo nano /opt/local/etc/php5/php.ini
    
If the file is empty, copy the development template file

    sudo cp /opt/local/etc/php5/php.ini-development /opt/local/etc/php5/php.ini

Search for the following values and make sure they're correct.

    post_max_size = 16M
    file_uploads = On
    upload_max_filesize = 16M

Now you're ready to restart apache and visit the site in your browser.

    sudo /opt/local/apache2/bin/apachectl restart

### Fake domain

If you happen to not have `yourdomain.com` registered, you can fake it by editing your `/etc/hosts` file and adding the following line

    127.0.0.1  yourdomain.com

### Launching your OpenPhoto site

Now you're ready to launch your OpenPhoto site. Point your browser to your host and you'll be taken to a setup screen. You'll need your cloud account credentials to continue.

Once you complete the 3 steps your site will be up and running and you'll be redirected there. The setup screen won't show up anymore.

### Performing setup again ###

If for any reason you want to go through the setup again you will need to delete the generated config file and refresh your browser.

    rm ~/Sites/yourdomain.com/src/userdata/configs/settings.ini

**ENJOY!**
