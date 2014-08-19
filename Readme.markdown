[![Build Status](https://travis-ci.org/photo/frontend.svg?branch=master)](http://travis-ci.org/photo/frontend)

### What is Trovebox?


### Install Trovebox in under 3 minutes

    # Installing Trovebox on Ubuntu and Apache
    # Run this from the command line as root.
    # As always, view any script before running it ;).

    curl https://raw.github.com/photo/frontend/master/documentation/guides/InstallationUbuntuApache.sh | /bin/bash

----------------------------------------

### How does Trovebox work?

Trovebox works similarly to Flickr, Smugmug and other photo sharing services with one major difference; you retain ownership and give Trovebox access to use them.
All photos, tags and comments are stored on your server or personal cloud accounts with companies like Amazon, Rackspace or Google.
This means you can easily switch between Trovebox services, use more than one at a time or stop using them altogether while retaining all of your photos, tags and comments.

----------------------------------------

### Why should I use Trovebox?

While Trovebox functions like many existing services it's drastically different for several reasons.

1.  **Ownership**
    Users can specify where their photos are stored. By default they are seamlessly stored in your [Amazon S3][s3] bucket.
1.  **Built in backups**
    Since you upload photos to your own [Amazon S3][s3] bucket it's like uploading and archiving your photos in one step.
1.  **Portability**
    Easily start off by signing up for a hosted Trovebox account and later switch to hosting the software yourself. There's no need to transfer your photos somewhere else since it's federated. It's like plug and play.
1.  **Durability**
    Your photos are not tied to any particular photo service. Because everything is open you can write your own web interface for your photos, choose between Trovebox hosting providers or install the Trovebox software on your own server.
1.  **Community**
    New features will be entirely community driven. Features with the most votes will get top priority in getting implemented. If you want a feature that doesn't have many votes you can implement it yourself and issue a pull request.

----------------------------------------

[aws]: http://aws.amazon.com/
[s3]: http://aws.amazon.com/s3/
[simpledb]: http://aws.amazon.com/simpledb/
