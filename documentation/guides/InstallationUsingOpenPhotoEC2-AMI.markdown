OpenPhoto / Installation for Ubuntu
=======================
#### OpenPhoto, a photo service for the masses

### Prerequisites

#### Cloud Accounts

Before setting up your server you'll need to make sure you have your cloud accounts set up. If you're using Amazon then make sure you've enabled both S3 and SimpleDb.

* http://aws.amazon.com/simpledb/
* http://aws.amazon.com/s3/

### Installing OpenPhoto

1. Sign Into AWS Management Console

1. Click AMIs under Images

1. Search for openphoto-instance

1. Selected the latest version by date or version

1. Click Launch

1. Click through EC2 options

1. Ensure security group allows HTTP & SSH access

1. Click back to Instances, wait for new instance to finish starting

1. Once started, click on the new instance in the web interface, copy the public DNS.
	
### Launching your OpenPhoto site

Now you're ready to launch your OpenPhoto site. Point your browser to your new EC2 host and you'll be taken to a setup screen. You'll need your cloud account credentiato continue.

Once you complete the 3 steps your site will be up and running and you'll be redirected there. The _setup_ screen won't show up anymore. If for any reason you want to go through the setup again you will need to delete the generated config file and refresh your browser.

    rm /var/www/yourdomain.com/src/configs/generated/settings.ini

**ENJOY!**

### TroubleShooting

* Can't write to config directory *
Open a terminal or SSH client, using your amazon ec2 ssh public key, ssh into the new instance as the ubuntu user.

	eg. ssh -i amazon-key.pem ubuntu@new-instance.amazonaws.com
	
Verify the apache user has write access to the /home/ubuntu/openphoto/src/configs directory.

	cd /home/ubuntu/openphoto/src/
	ls -ld ./configs
	
	drwxr-xr-x 3 www-data www-data 4096 2011-08-20 02:37 configs


	

