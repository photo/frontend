OpenPhoto / Installation for SUSE/openSUSE
=======================
#### OpenPhoto, a photo service for the masses

## OS: Linux openSUSE 11.4+

This guide instructs you on how to install OpenPhoto on an openSUSE server.

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
Once you've confirmed that your cloud account is setup you can get started on your server. For that you'll need to have _Apache_, _PHP_ and _curl_ installed with a few modules.

    zypper in apache2 php5 apache2-mod-php5 php5-curl php5-mcrypt

And if you are going to use MySQL install `php5-mysql`.

Ensure you have mod_rewrite enabled

    a2enmod rewrite

There are also a few optional but recommended packages and modules. Add the PHP Extensions repo.

    zypper ar http://download.opensuse.org/repositories/server:/php:/extensions/openSUSE_11.4 php:extensions    
    zypper in php5-imagick php5-oauth php5-APC exiftran
    a2enmod deflate
    a2enmod expires
    a2enmod headers

----------------------------------------

### Installing OpenPhoto

Download and install the source code. We recommend `/srv/www/htdocs/yourdomain.com` but you can use any directory you'd like.

#### Using git clone

    zypper in git
    git clone https://github.com/photo/frontend.git /srv/www/yourdomain.com
    chown -R wwwrun:www /srv/www/htdocs/yourdomain.com

#### Using tar

    cd /var/www
    wget https://github.com/photo/frontend/tarball/master -O openphoto.tar.gz
    tar -zxvf --group=www --owner=wwwrun openphoto.tar.gz
    mv openphoto-frontend-* yourdomain.com
    chown -R wwwrun:www yourdomain.com

Assuming that this is a development machine you can make the config writable by the user Apache runs as. Most likely `wwwrun`.

    mkdir /srv/www/htsdocs/yourdomain.com/src/userdata
    chown wwwrun:www /srv/www/htdocs/yourdomain.com/src/userdata

----------------------------------------

### Setting up Apache and PHP

#### Apache

You'll need to copy the sample virtual host configuration file from the source to `/etc/apache2/vhosts.d`.

    cp /srv/www/htdocs/yourdomain.com/src/configs/openphoto-SUSE-vhost.conf /etc/apache2/vhosts.d/yourdomain.com.conf

Now you'll need to replace instances of `/path/to/openphoto/html/directory` with `/srv/www/htdocs/yourdomain.com/src/html` or wherever you placed the code.

    vi /etc/apache2/vhosts.d/yourdomain.com.conf

By default, any access to ini files is denied with a "Not Found" 404 HTTP code.  To enable a 404, or Forbidden return code, change the following lines in the virtual host file.

Uncomment:

    # 403 Forbidden for ini files
    #RewriteRule \.ini$ - [F,NC]

Comment:

  # 404 Not Found for ini files
  AliasMatch \.ini$	/404

### PHP

You should also verify that your `php.ini` file has a few important values set correctly.

    vi /etc/php5/apache2/php.ini

Search for the following values and make sure they're correct.

    file_uploads = On
    upload_max_filesize = 16M
    post_max_size = 16M

Now you're ready to restart apache and visit the site in your browser.

    rcapache2 restart

### Launching your OpenPhoto site

Now you're ready to launch your OpenPhoto site. Point your browser to your host and you'll be taken to a setup screen. You'll need your cloud account credentials to continue.

Once you complete the 3 steps your site will be up and running and you'll be redirected there. The _setup_ screen won't show up anymore. If for any reason you want to go through the setup again you will need to delete the generated config file and refresh your browser.

    rm /srv/www/htdocs/yourdomain.com/src/configs/generated/settings.ini

**ENJOY!**

