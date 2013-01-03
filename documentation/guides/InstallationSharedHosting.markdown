# OpenPhoto / Installation for Shared Hosting

#### OpenPhoto, a photo service for the masses

## Installation on Shared Hosting

This guide instructs you on how to install OpenPhoto on shared hosting sites such as Dreamhost or Bluehost. OpenPhoto can be difficult to install for users not experienced with PHP, Apache, or MySQL. If you'd like to use OpenPhoto without installing the software yourself, <a href="http://openphoto.me">get started here</a>.

If you're using Dreamhost <a href="https://github.com/photo/frontend/blob/master/documentation/guides/InstallationDreamhost.markdown">we have a community-written guide for Dreamhost users.</a>

*OpenPhoto should be installed in the root directory of a domain or subdomain.*

Variables:

- *YOURDOMAIN*:the subdomain or domain for hosting OpenPhoto
- *YOURNAME*: your shared hosting username
- *OpenPhotoRoot*: the root directory for OpenPhoto (e.g., ~/openphoto)

### Before you install OpenPhoto
This guide assumes you have:
1. Checked that your webhost supports MySQL and PHP
2. Shell or FTP access to your web server
3. An FTP or SSH client
4. A web browser of choice
5. An external cloud service account on Amazon or Dropbox (if you want to store your photos there)


### The short version
Here's the short version of the instructions for those already comfortable with installing software on a web server. You can check out the detailed instructions below for more information on each step.

1. Download the latest version of OpenPhoto from Github. <a href="https://github.com/photo/frontend/archive/master.zip">Direct link to latest version as a .zip file</a>

2. Prepare your cloud storage option and have your credentials ready. (Optional)


3. Create a new MySQL database and a new user for that database. Remember the hostname (the default should be fine), database name, username, and password. Your webhost may have a MySQL control panel such as PhpMyAdmin that you can do this in.

4. Extract the folder to the root folder of your website.

5. Create the following folders and chmod them to 775:
        mkdir OpenPhotoRoot/src/html/assets/cache
        chmod 775 OpenPhotoRoot/src/html/assets/cache
        
        mkdir OpenPhotoRoot/src/html/photos
        chmod 775 OpenPhotoRoot/src/html/photos
        
        mkdir OpenPhotoRoot/src/userdata
        chmod 775 OpenPhotoRoot/src/userdata
        
6. Visit your website and follow the instructions. 

That's it! OpenPhoto should now be installed. Because there are so many special cases and things that could go wrong, you may want to read the full instructions below.

### The detailed version

1. Download OpenPhoto from Github. <a href="https://github.com/photo/frontend/archive/master.zip">Direct link to latest version as a .zip file</a>. You can also do the following:

        wget https://github.com/photo/frontend/tarball/master -O openphoto.tar.gz
        tar -zxvf openphoto.tar.gz
        mv openphoto-frontend-* OpenPhotoRoot

1. Install any dependencies or modules needed.
Your webhost may include them by default, but if not, check their documentation. Here's what you'll need if they're not already installed:

* OAuth for authentication
* ImageMagick or GD for photo rendering

The method of installing these varies by webhost. Some webhosts let you install them by yourself; others will install these for you if you contact them. Again, consult their documentation.


1. Create an account at Amazon AWS or Dropbox if you plan to use them. Save your keys since you'll need them soon.  
        
1. Create the following directories.
        the cache:
        mkdir OpenPhotoRoot/src/html/assets/cache
        chmod 775 OpenPhotoRoot/src/html/assets/cache
        
        to store your photos if you're planning on local storage:
        mkdir OpenPhotoRoot/src/html/photos
        chmod 775 OpenPhotoRoot/src/html/photos
        
        to store userdata:
        mkdir OpenPhotoRoot/src/userdata
        chmod 775 OpenPhotoRoot/src/userdata


1. Configure the subdomain or domain.
You may have to add the domain if you're bringing in a new domain. Consult your webhost's documentation if needed. Depending on your webhost you may have to visit multiple areas of the site to configure everything. But here's what you need to set up.
    
1. Visit your control panel for managing databases and create a new database and new user for the database. Remember the hostname (the default should be fine), database name, username, and password. You'll need these during setup.

1. After waiting a sufficient amount of time for the subdomain name to propagate, use the browser to connect to the new subdomain.  You should see a setup page for OpenPhoto which will allow you to configure your OpenPhoto project.

Step One: Enter your email address, password.

Step Two: Select your image renderer (ImageMagick or GD are the most common options), database (MySQL or InnoDB), and storage (Local filesystem, Amazon S3, Amazon S3+Dropbox, Local filesystem+Dropbox).

Step Three: Enter your credentials for your database, Amazon S3, or Dropbox.

ENJOY! 

### Troubleshooting

#### Setup page looks strange (black and white, unstyled)
If the setup page is not colorful and well formatted, then the css and javascript files are most likely not being loaded.  Possible causes:

- Web directory root is not properly set (check control panel for the subdomain)
- src/html/assets/cache directory is not writeable by Apache (check your permissions--they should be set to 775)

#### I set my permissions to 775 and it's still not working. What gives? (or safe_mode issues)


#### Error setting up the database
Double check all the parameters. Check your database control panel and verify that everything is correct.

**ENJOY!**

