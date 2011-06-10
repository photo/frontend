OpenPhoto
=======================
#### OpenPhoto, a photo service for the masses

----------------------------------------

### What is OpenPhoto?

####Think of OpenPhoto as a WordPress for photo sharing and management.####
OpenPhoto is federated - meaning it's agnostic to where photos and their metadata are stored.
This is incredibly important since it allows you to decide where you want your photos stored and whether you want to sign up at OpenPhoto.me or host the software yourself.
Because of it's federated nature you can sign up at OpenPhoto.me and later transfer to another hosting provider or install the software onto your own servers.

----------------------------------------

### Why should I use OpenPhoto?

While OpenPhoto functions like many existing services it's drastically different for several reasons.

1.  **Ownership**  
    Users can specify where their photos are stored. By default they are seamlessly stored in your [Amazon S3][s3] bucket.
1.  **Built in backups**  
    Since you upload photos to your own [Amazon S3][s3] bucket it's like uploading and archiving your photos in one step.
1.  **Portability**  
    Easily start off by signing up for a hosted OpenPhoto account and later switch to hosting the software yourself. There's no need to transfer your photos somewhere else since it's federated. It's like plug and play.
1.  **Durability**  
    Your photos are not tied to any particular photo service. Because everything is open you can write your own web interface for your photos, choose between OpenPhoto hosting providers or install the OpenPhoto software on your own server.
1.  **Community**  
    New features will be entirely community driven. Features with the most votes will get top priority in getting implemented. If you want a feature that doesn't have many votes you can implement it yourself and issue a pull request.

----------------------------------------

### What if I use Flickr or Smugmug?

If you're using Flickr or Smugmug you should consider switching to OpenPhoto.
The more photos and time you invest on a propietary photo sharing service the more devastated you're going to be once they shut down or no longer appeal to you.

There are importing tools available to make the switch easy.

----------------------------------------

[aws]: http://aws.amazon.com/
[s3]: http://aws.amazon.com/s3/
[simpledb]: http://aws.amazon.com/simpledb/
