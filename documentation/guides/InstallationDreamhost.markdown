# OpenPhoto / Installation for Dreamhost

#### OpenPhoto, a photo service for the masses

## Installation on Dreamhost

This guide instructs you on how to install OpenPhoto on Dreamhost.
The guide assumes a MySQL installation using the local filesystem for photo storage.

*OpenPhoto should be installed in the root directory of a domain or subdomain.*

Variables:

- *YOURDOMAIN*:the subdomain or domain for hosting OpenPhoto
- *YOURNAME*: your Dreamhost username
- *OpenPhotoRoot*: the root directory for OpenPhoto (e.g., ~/openphoto)

### Steps

1. Download and install OpenPhoto

		wget https://github.com/photo/frontend/tarball/master -O openphoto.tar.gz
		tar -zxvf openphoto.tar.gz
		mv openphoto-frontend-* OpenPhotoRoot

1. Create directories

		mkdir OpenPhotoRoot/src/html/assets/cache
		chmod 775 OpenPhotoRoot/src/html/assets/cache
		
		mkdir OpenPhotoRoot/src/html/photos
		chmod 775 OpenPhotoRoot/src/html/photos
		
		mkdir OpenPhotoRoot/src/userdata
		chmod 775 OpenPhotoRoot/src/userdata
	
1. Configure the subdomain or domain.
Go to the [Dreamhost control panel for managing domains](https://panel.dreamhost.com/index.cgi?tree=domain.manage)

	Domain settings:

	- Select the *Fully Hosted* portion of the configuration panel.
	- *Do you want the www in your URL?* Select "Leave it alone"
	- *Web directory:* Set to *OpenPhotoRoot*/src/html
	- *PHP mode:* Select the latest version of PHP (use FastCGI configuration)

1. Go to the [Dreamhost control panel for managing databases](https://panel.dreamhost.com/index.cgi?tree=goodies.mysql)
and create a new database and a new user for the database.  Remember the hostname (the default should be fine), database name, user name, and password, since you'll need this information during the setup.

1. After waiting a sufficient amount of time for the subdomain name to propagate, use the browser to connect to the new subdomain.  You should see a setup page for OpenPhoto which will allow you to configure your OpenPhoto project.

	- *Select Database:* MySQL
	- *Select File System:* Local filesystem
	- *Enter your local file system credentials:* /home/USERNAME/OpenPhotoRoot/src/html/photos
	- *File system hostname for download URL (Web accessible w/o "http://"):* YOURDOMAIN.com/photos


### Troubleshooting

#### Setup page looks strange (black and white, unstyled)
If the setup page is not colorful and well formatted, then the css and javascript files are most likely not being loaded.  Possible causes:

- Web directory root is not properly set (check control panel for the subdomain)
- src/html/assets/cache directory is not writeable by Apache (check permissions)

#### Error setting up the database
Double check all the parameters.  Open the Dreamhost control panel for databases.

	
### Launching your OpenPhoto site

Now you're ready to launch your OpenPhoto site. Point your browser to your host and you'll be taken to a setup screen.

Once you complete the three steps your site will be up and running.

**ENJOY!**

