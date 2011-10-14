<img src="frontend/raw/master/files/creative/logo.png" style="width:234px; height:43px; margin:auto;">


### What is OpenPhoto?

1.  [FAQ][faq], Answers to the most common questions.
1.  [API][api], Documentation to get started building applications on OpenPhoto.
1.  [Schemas][schemas], Description of what the different objects in OpenPhoto look like (i.e. a photo or a comment).
1.  [Guides][guides], Detailed guides to help you get the OpenPhoto software installed on various systems.

----------------------------------------

### How does OpenPhoto work?

OpenPhoto works similarly to Flickr, Smugmug and other photo sharing services with one major difference: you retain ownership and give OpenPhoto access to use them.
All photos, tags and comments are stored in your personal cloud accounts with companies like Amazon, Rackspace or Google. 
This means you can easily switch between OpenPhoto services, use more than one at a time or stop using them altogether while retaining all of your photos, tags and comments.

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
[api]: documentation/api/Api.markdown
[faq]: documentation/faq/Faq.markdown
[schemas]: documentation/schemas/Schemas.markdown
[guides]: documentation/guides/Guides.markdown
