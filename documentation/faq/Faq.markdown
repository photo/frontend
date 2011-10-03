Documentation
=======================
#### OpenPhoto, a photo service for the masses

This is a list of common questions that people have about OpenPhoto. 
If you have one which is not on this list send us a message via <a href="http://twitter.com/openphoto">Twitter</a> or <a href="mailto:hello@openphoto.me">email</a>.

### What exactly is OpenPhoto?

The short answer is that OpenPhoto is a way to store and share your photos without giving up control and ownership of them.

The long answer is that OpenPhoto consists of two parts: a specification and an implementation.

The specification is a set of guidelines that define exactly how your photos are stored, how they are accessed and much more. 
This helps make the entire system open and enables other developers to build functionality on top of your photos. 
The documentation makes up the majority of the specification.

The implementation is code which adheres to the specification and provides functionality. 
The OpenPhoto.me website is an example where the funtionality provided is the ability to store and share photos. 
Another example would be if a developer created an Instagram like application adhering to the specification. 
That would be another implementation which provides the functionality of easily sharing photos from your phone with filters.

----------------------------------------

### What makes OpenPhoto different from Flickr, Smugmug, iCloud or any other service?

Normally you pay one company to store your photos and to provide services to share them. 
This means that you can't do much if they raise their prices, shut down their service or another site comes along.

These sites may offer APIs but they typically enable addon services and rarely competing services. 
Your photos are still stored on the company's servers who provide the base service.

With OpenPhoto you can switch between services, use more than one at a time or stop using the service and continue to have your photos stored. 

----------------------------------------

### If OpenPhoto is open sourced then why isn't it free?

For clarity, OpenPhoto is both free and open sourced (FOSS). 
Typically what you end up paying for is storage from someone like Amazon or Rackspace. 

Amazon offers 5GB of storage for free which means if you don't need any additional space then using OpenPhoto is entirely free.

----------------------------------------

### How much does the storage cost?

It depends on who you use but here are some guidelines.

1. 20GB of storage on Amazon S3 costs ≈_$2/mo_
1. A 250KB photo served up 4,000 times on Amazon S3 costs ≈_$.10/mo_
1. A simpleDb database for a personal account is typically _free_

----------------------------------------

### When will the OpenPhoto software be completed?

You can set up the OpenPhoto software using [any of the guides][guides].

----------------------------------------

### When will the hosted version of OpenPhoto be available?

We are aiming to have it ready by the end of November (2011).

[guides]: ../guides/Guides.markdown

