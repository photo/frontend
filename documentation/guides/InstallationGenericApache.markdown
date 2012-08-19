OpenPhoto / Installation for Linux - Apache
=======================
#### OpenPhoto, a photo service for the masses

## Generic Installation Guide
This guide is intended to be usable by users of any distribution of Linux running a standard LAMP stack. As such, it is written at a rather high level of abstraction. For distribution-specific details, find a different guide on the sidebar to the left.


### Prerequisites

#### Database Options
Openphoto can use either MySql or Amazon Simpledb to store metadata about photos.

##### MySql 
You'll need to provide credentials for a MySql database. If the database doesn't already exist it will be created. If the user doesn't have `CREATE DATABASE` permissions then make sure it's already created.

##### Simpledb
First, make sure you're signed up.

	http://aws.amazon.com/simpledb/

After that, pick up an Access Key ID and Secret Access Key from:

	https://portal.aws.amazon.com/gp/aws/securityCredentials

#### File System Options
To store photos, openphoto has a number of options.

##### Amazon S3
First, sign up here:

	http://aws.amazon.com/s3/

After that, pick up an Access Key ID and Secret Access Key from:

	https://portal.aws.amazon.com/gp/aws/securityCredentials

You will also need a bucket name, but this can be anything. Openphoto will create it if necessary.

##### Local Filesystem
Fairly self-explanitory. You must define the folder where you wish to store your photos (Be sure to make this writable by the user Apache runs as!) and the web-accessable hostname which openphoto will use to construct download URLs. This will either be the domain name assigned to the server, or the IP address. Include any necessary subdirectories. Do not include http://.

E.g.:

	example.com/photos
	127.0.0.1/photos

##### Dropbox
Dropbox is a backup option which can be combined with either S3 or the locl filesystem. To enable it, you need a Key and Secret from:

	https://www.dropbox.com/developers/apps

Be sure to allow Full dropbox access. The folder name can be anything, and will be created if necessary.

#### Necessary software

To run openphoto, ensure the following software is installed:
	
	* Apache
	* PHP
	* Apache PHP module
	* PHP Curl module
	* PHP mcrypt module
	* PHP apc module
	* libpcre development libraries and headers
	* PHP PEAR

One or more of:

	* PHP imagemagick module
	* PHP graphicsmagick module
	* PHP GD module (not recommended; last resort)

Non-essential but recommended:

	* exiftran
	* PHP oauth module
	* Mysql (if you will be using it)
	* PHP pdo mysql module (ditto)

Be sure to modify php.ini to enable each PHP module. 

The best place to find PHP modules is your distro's standard repository. If you can't find it packaged there, use `pecl install <package name>`. Find packages at http://pecl.php.net/.

Enable the following Apache modules:

	* rewrite
	* deflate
	* expires
	* headers
	
NOTE: If this is your first time installing Apache on your current distro, be sure to read your distro's documentation on how to set up Apache and how to set up vhosts in Apache. These vary wildly by distro and you are expected to know what to do!
	
----------------------------------------

### Installing OpenPhoto

Download and install the source code. We recommend the default apache root (Often `/var/www`, or `/srv/http`, check your distro documentation for specifics), but it can go anywhere if Apache's user has permissions to access there.

#### Using git clone

    apt-get install git-core
    git clone git://github.com/photo/frontend.git <install location>/yourdomain.com

#### Using tar

    cd <install location>
    wget https://github.com/photo/frontend/tarball/master -O openphoto.tar.gz
    tar -zxvf openphoto.tar.gz
    mv openphoto-frontend-* yourdomain.com

Once installed, create these three directories for configuration files:

	* yourdomain.com/src/userdata
	* yourdomain.com/src/html/photos
	* yourdomain.com/src/html/assets/cache
	
Then chown them all to the user Apache will run as.


----------------------------------------

### Setting up Apache and PHP

#### Apache

You'll need to copy the sample virtual host configuration file from the source to wherever Apache stores virtual host configuration files in your distro. They are stored in:

	<install location>/yourdomain.com/src/configs/openphoto-vhost.conf
	
After copying, edit the file and replace all instances of `/path/to/openphoto/html/directory` with `<install location>/yourdomain.com/src/html`.

It may be necessary to enable openphoto's vhost and disable the default, here. This differs by distro, so check your distro's Apache docs.

By default, any access to ini files is denied with a "Not Found" 404 HTTP code.  To enable a 404, or Forbidden return code, change the following lines in the virtual host file.

Uncomment:

    # 403 Forbidden for ini files
    #RewriteRule \.ini$ - [F,NC]

Comment:

  # 404 Not Found for ini files
  AliasMatch \.ini$	/404
  
### PHP

You should also verify that your `php.ini` file has a few important values set correctly.

Find php.ini and open in with a text editor.

Search for the following values and make sure they're correct.

    file_uploads = On
    upload_max_filesize = 16M
    post_max_size = 16M

Now, the site should be ready to use. Start/Restart Apache, and open the host in your browser. You should see a setup screen. You'll need your cloud account credentials to continue.

Once you complete the 3 steps your site will be up and running and you'll be redirected there. The _setup_ screen won't show up anymore, but you can rerun it via the Manage page.

** ENJOY! **