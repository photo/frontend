## Self-Hosted OpenPhoto FAQ
### How can I install OpenPhoto?
We have lots of guides to help you through the installation process. <a href="http://theopenphotoproject.org/documentation">Check them out here</a>. We welcome <a href="http://github.com/photo/frontend">contributions on Github</a> if you see one that's incomplete or want to add one.

### I'm using a shared hosting webservice. Can I still install OpenPhoto?
Yes, but you may have to configure some settings differently. Check out our <a href="https://github.com/photo/frontend/blob/master/documentation/guides/InstallationSharedHosting.markdown">shared hosting guide</a>. If you're using Dreamhost we have <a href="https://github.com/photo/frontend/blob/master/documentation/guides/InstallationDreamhost.markdown">a community-written guide for Dreamhost users</a>.

### Can I install OpenPhoto to a subdirectory of my site?
Not yet, but it's on our wishlist.

### How does support for multiple users work?
We support multiple users through separate domains. <a href="https://github.com/photo/frontend/issues/318">See this issue on why it works that way.</a>

### What cloud services are supported for self-hosted sites?
OpenPhoto currently supports Amazon S3 and Dropbox for self-hosted sites.

### Can I import my photos from other photo sites?
Yes, you can use the scripts available on our Github. We have <a href="https://github.com/photo/export-flickr">a Flickr export script</a> and an <a href="https://github.com/photo/import">import script</a> available on Github. You can also configure something like Ifttt or Pi.pe to continuously import photos from a site.

### Is your mobile app available for self-hosted users?
Yes! Download the app <a href="http://itunes.com/apps/theopenphotoapp">for iOS</a> or <a href="https://play.google.com/store/apps/details?id=me.openphoto.android.app">Android</a>.

### I just changed my domain name. Why am I getting prompted for new settings?
This is normal. OpenPhoto configs are bound to a site, not a server. Since a site is defined by its hostname, you can have multiple sites on one host provided that they have different hostnames. So go ahead and change your settings.

### I encountered a security issue in OpenPhoto. What's the best way to let you know?
<a href="https://github.com/photo/frontend/issues">Report it as an issue at Github</a>. If you don't feel comfortable exposing a security issue, reach out to someone on the core team and email us individually.

### Something broke. How do I let you know?
Check <a href="https://github.com/photo/frontend/issues">our issue tracker at Github</a> first; if the issue you're encountering isn't already there, then start a new issue and describe what you ran across. The more detail you can give us, the better.

### Help! I'm stuck and I have questions.
If you have questions we're always around to help. We've got several contact options listed on the <a href="http://theopenphotoproject.org/contribute">contribute</a> page.
