# OpenPhoto / Installation for Shared Hosting

#### OpenPhoto, a photo service for the masses

## Installation on Shared Hosting

This guide instructs you on how to install OpenPhoto on shared hosting sites such as Dreamhost or Bluehost. OpenPhoto can be difficult to install for users not experienced with performing such installations. If you'd like to use OpenPhoto without installing the software yourself, <a href="http://openphoto.me">get started here</a>.

If you're using Dreamhost <a href="https://github.com/photo/frontend/blob/master/documentation/guides/InstallationDreamhost.markdown">we have a community-written guide for Dreamhost users</a>. Because every webhost is unique, we welcome additions to this guide as well as guides on installing OpenPhoto on your webhost.

*OpenPhoto should be installed in the root directory of a domain or subdomain.*

Variables:

- *OpenPhotoRoot*: the root directory for OpenPhoto (e.g., ~/openphoto)

### Before you install OpenPhoto
This guide assumes you have:
* Checked that your webhost supports MySQL and PHP
* Shell or FTP access to your web server
* An FTP or SSH client
* A web browser of choice
* A text editor (optional)
* An external cloud service account on Amazon or Dropbox (if you want to store your photos there)


### The short version
Here's the short version of the instructions for those already comfortable with installing software on a web server. You can check out the detailed instructions below for more information on each step.

1. Download the latest version of OpenPhoto from Github and extract it to the root folder of your website. <a href="https://github.com/photo/frontend/archive/master.zip">Direct link to latest version as a .zip file</a>

2. Prepare your cloud storage option and have your credentials ready. (Optional)

3. Create a new MySQL database and a new user for that database. Remember the hostname (the default should be fine), database name, username, and password. Your webhost may have a MySQL control panel such as PhpMyAdmin that you can do this in.

4. Create the following folders and chmod them to 775:

        mkdir OpenPhotoRoot/src/html/assets/cache
        chmod 775 OpenPhotoRoot/src/html/assets/cache
        
        mkdir OpenPhotoRoot/src/html/photos
        chmod 775 OpenPhotoRoot/src/html/photos
        
        mkdir OpenPhotoRoot/src/userdata
        chmod 775 OpenPhotoRoot/src/userdata
        
5. Visit your website and follow the instructions. 

That's it! OpenPhoto should now be installed. Because there are so many special cases specific to individual webhosts along with things that could go wrong, you may want to read the full instructions below.

### The detailed version

#### 1. Download OpenPhoto from Github.
<a href="https://github.com/photo/frontend/archive/master.zip">Direct link to latest version as a .zip file</a>. You can also do the following:

        wget https://github.com/photo/frontend/tarball/master -O openphoto.tar.gz
        tar -zxvf openphoto.tar.gz
        mv openphoto-frontend-* OpenPhotoRoot

#### 2. Install any dependencies or modules needed.
Your webhost may include them by default. Check their documentation. Here's what you'll need:

* The Pecl extension `oauth` for authentication
* ImageMagick or GD for photo rendering

The method of installing these varies by webhost. Some webhosts let you install them by yourself; others will install these for you if you contact them.

#### 3. Create your cloud accounts (if you plan on using them).
Create an account at <a href="https://aws.amazon.com/s3">Amazon AWS</a> or <a href="http://www.dropbox.com">Dropbox</a> if you plan to use them. Create a new bucket (S3) or app (Dropbox). Save your keys since you'll need them soon.  

At Amazon:    
* Sign in and visit <a href="https://console.aws.amazon.com/s3/home">the S3 panel</a> and select Create a New Bucket. 
* Give your bucket a name and select a region, then select Create. 
* <a href="https://portal.aws.amazon.com/gp/aws/securityCredentials">Obtain your access keys</a> and save them.

At Dropbox:
* Sign in and create a folder for your photos to go in. 
* Visit <a href="https://www.dropbox.com/developers/apps">the developers page</a>
* Select Create an App, and select Core API for App Type and Full Dropbox Access.

This will give you a development app to use for your photos. Save your access keys; you'll need them soon.

#### 4. Create a database and user.
Visit your control panel for managing databases and create a new database and new user for the database. Give the user `CREATE DATABASE` privileges if you haven't created the database yet. Remember the hostname (the default should be fine), database name, username, and password. You'll need these during setup.

#### 5. Configure the subdomain or domain.
You may have to add the domain if you're bringing in a new domain. Consult your webhost's documentation if needed. Depending on your webhost you may have to visit multiple areas of the site to configure everything, or you may have to configure these separately. Here's what you need to set up.

* PHP: Select the latest version, FastCGI configuration if available
* Web directory: OpenPhotoRoot/src/html

If you can't set the web directory to OpenPhotoRoot/src/html through a web interface, you can create an .htaccess file in the root directory. Open a text editor and include the following:

       RewriteEngine on
       RewriteBase /
       RewriteCond %{HTTP_HOST} ^your.domain.com$ [NC,OR]
       RewriteCond %{REQUEST_URI} !src/html/
       RewriteRule (.*) /src/html/$1 [L]
       
Save the file as .htaccess and upload it to the root folder of your site if you haven't already, along with the OpenPhoto folder.

#### 6. Upload OpenPhoto.
Upload the contents of the downloaded OpenPhoto folder to the root directory if you haven't already. You can do this with an FTP or SSH client.
        
#### 7. Create the following directories.

        the cache:
        mkdir OpenPhotoRoot/src/html/assets/cache
        chmod 775 OpenPhotoRoot/src/html/assets/cache
        
        to store your photos if you're planning on local storage:
        mkdir OpenPhotoRoot/src/html/photos
        chmod 775 OpenPhotoRoot/src/html/photos
        
        to store userdata:
        mkdir OpenPhotoRoot/src/userdata
        chmod 775 OpenPhotoRoot/src/userdata

You can also do this with your FTP client. If you do, the user and group should have read, write, and execute privileges. World should have read and execute privileges.

#### 8. Install OpenPhoto
After waiting a sufficient amount of time for the subdomain name to propagate, use the browser to connect to the new subdomain.  You should see a setup page for OpenPhoto which will allow you to configure your OpenPhoto site.

* Enter your email address and select a password.

* Select your image renderer (ImageMagick or GD are the most common options), database (MySQL or InnoDB), and storage (Local filesystem, Amazon S3, Amazon S3+Dropbox, Local filesystem+Dropbox).

* Enter your credentials for your database, Amazon S3, or Dropbox.

**ENJOY!**

### Troubleshooting

#### Setup page looks strange (black and white, unstyled)
If the setup page is not colorful and well formatted, then the css and javascript files are most likely not being loaded.  Possible causes:

- Web directory root is not properly set (check control panel for the subdomain)
- src/html/assets/cache directory is not writeable by Apache (check your permissions)

#### My webhost doesn't recognize OpenPhotoRoot/src/html as the index directory.
You can set this in the .htaccess page at OpenPhotoRoot. If your webhost lets you set this through the web panel you can also do that there.

#### Error setting up the database
Double check all the parameters. Check your database control panel and verify that everything is correct. Also double check that the user for your database has permission to create a database if you haven't already created a database.

####Help! I'm stuck and I have questions!
If you have questions we're always around to help. We've got several contact options listed on the <a href="http://theopenphotoproject.org/contribute">contribute</a> page.
