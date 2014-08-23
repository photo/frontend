[![Build Status](https://travis-ci.org/photo/frontend.svg?branch=master)](http://travis-ci.org/photo/frontend)

<img src="https://raw.githubusercontent.com/photo/frontend/master/files/creative/logo.png" width="250">

### What is Trovebox?
Trovebox is software that helps you manage, organize and share photos. It includes [web](https://github.com/photo/frontend) and mobile apps for [Android](https://github.com/photo/mobile-android) and [iOS](https://github.com/photo/mobile-ios). The goal of Trovebox is to be software which people love to use and helps them preserve their digial media files.

The development of Trovebox was in large part funded by the [Shuttleworth Foundation](https://www.shuttleworthfoundation.org/) through a fellowship grant.

<img src="https://raw.githubusercontent.com/photo/frontend/master/files/creative/shuttleworth-funded.png" width="250">

### Install Trovebox in under 3 minutes

    # Installing Trovebox on Ubuntu and Apache
    # Run this from the command line as root.
    # As always, view any script before running it ;).

    curl https://raw.github.com/photo/frontend/master/documentation/guides/InstallationUbuntuApache.sh | /bin/bash

----------------------------------------

### Trovebox UI

<a href="https://raw.githubusercontent.com/photo/frontend/master/files/creative/screenshots/web/gallery.jpg"><img src="https://raw.githubusercontent.com/photo/frontend/master/files/creative/screenshots/web/gallery-t.jpg" width="300" hspace="20" vspace="20"></a>
<a href="https://raw.githubusercontent.com/photo/frontend/master/files/creative/screenshots/web/lightbox.jpg"><img src="https://raw.githubusercontent.com/photo/frontend/master/files/creative/screenshots/web/lightbox-t.jpg" width="300" hspace="20" vspace="20"></a>
<a href="https://raw.githubusercontent.com/photo/frontend/master/files/creative/screenshots/web/detail.jpg"><img src="https://raw.githubusercontent.com/photo/frontend/master/files/creative/screenshots/web/detail-t.jpg" width="300" hspace="20" vspace="20"></a>
<a href="https://raw.githubusercontent.com/photo/frontend/master/files/creative/screenshots/web/upload.jpg"><img src="https://raw.githubusercontent.com/photo/frontend/master/files/creative/screenshots/web/upload-t.jpg" width="300" hspace="20" vspace="20"></a>

*<sub>Photos by [Duncan Rawlinson](http://duncan.co/)</sub>*

----------------------------------------

### How does Trovebox work?

Trovebox works similarly to Flickr or Smugmug. We've focused on making sure that the design, UI and UX is as good as our commercial alternatives. You can upload, view and share photos through the web or by using our mobile apps.

Unlike most services you can install Trovebox on your own server and connect it to cloud storage services like Dropbox or Amazon S3. We wanted to make FOSS software that's as good as what the commercial sites offer without having to give up privacy.

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
