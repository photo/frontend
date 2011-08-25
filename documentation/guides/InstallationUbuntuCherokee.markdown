OpenPhoto / Installation for Ubuntu
=======================
#### OpenPhoto, a photo service for the masses

## OS: Linux Ubuntu Server 10.04+

This guide instructs you on how to install OpenPhoto on Cherokee Web Server on an Ubuntu server.
To have a recent version of Cherokee, I advice to add the ppa maintained with latest version. You can add it with the command
    add-apt-repository ppa:cherokee-webserver/ppa

----------------------------------------

### Prerequisites

#### Cloud Accounts

Before setting up your server you'll need to make sure you have your cloud accounts set up. If you're using Amazon then make sure you've enabled both S3 and SimpleDb.

* http://aws.amazon.com/simpledb/
* http://aws.amazon.com/s3/

#### Server Packages and Modules
Once you've confirmed that your cloud account is setup you can get started on your server. For that you'll need to have _Cherokee_, _PHP_ and _curl_ installed with a few modules.

    apt-get install cherokee
    apt-get install php5-fpm
    apt-get install php5-curl

There are also a few optional but recommended packages and modules.

    apt-get install php5-imagick
    apt-get install exiftran

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
    tar -zxvf --group=www-data --owner=www-data openphoto.tar.gz
    mv openphoto-frontend-* yourdomain.com

Assuming that this is a development machine you can make the config writable by the user Apache runs as. Most likely `www-data`.

    mkdir /var/www/yourdomain.com/src/configs/generated
    chown www-data:www-data /var/www/yourdomain.com/src/configs/generated

----------------------------------------

### Setting up Cherokee and PHP

### PHP

You should also verify that your `php.ini` file has a few important values set correctly.

    vi /etc/php5/fpm/php.ini

Search for the following values and make sure they're correct.

    file_uploads = On
    upload_max_filesize = 16M
    post_max_size = 16M

Now you're ready to restart php5-fpm to use the new parameters.

    /etc/init.d/php5-fpm restart

### Cherokee

Launch the administration web interface and connect to it

    cherokee-admin
    firefox http://localhost:9090

Go the sections "Vservers" and create a new vserver with the + button

In the "Languages" sub-section, choose "PHP" and click "Add"

Set the Document Root to "/var/www/yourdomain.com/src/html" and click "Next"

Enter the Vhost Name the configuration will serve and set the log configuration; click "Create"

Now, go to the rules definition and add a "File Exists" rules, invert the rule with "Not" button and set the following parameters:
* Match any file: enable
* Use I/O Cache: enable
* Only match files: disable
* If dir, check Index files: disable

Go to the tab "Handler", set a "Redirection" handler with the following parameters:
* Show: Internal
* Regular Expression: `^/(.*)\?*$`
* Substitution: `index.php?__route__=/$1`

Click "Add".

Press the button "Save", and restart Cherokee.

### Launching your OpenPhoto site

Now you're ready to launch your OpenPhoto site. Point your browser to your host and you'll be taken to a setup screen. You'll need your cloud account credentials to continue.

Once you complete the 3 steps your site will be up and running and you'll be redirected there. The _setup_ screen won't show up anymore. If for any reason you want to go through the setup again you will need to delete the generated config file and refresh your browser.

    rm /var/www/yourdomain.com/src/configs/generated/settings.ini

**ENJOY!**

