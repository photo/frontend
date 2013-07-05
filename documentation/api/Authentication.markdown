Authentication using OAuth 1.0a
=======================

### Using OAuth (1.0a)

A full introduction to OAuth is beyond the scope of the OpenPhoto documentation.
In all reality you probably don't need to understand all the ins and outs of OAuth; just grab one of our libraries and start building.

* <a href="https://github.com/photo/openphoto-php">openphoto/openphoto-php</a> - Our PHP language binding.
* <a href="https://github.com/photo/openphoto-ruby">openphoto/openphoto-ruby</a> - Our Ruby language binding.
* <a href="https://github.com/photo/openphoto-python">openphoto/openphoto-python</a> - Our Python language binding.
* <a href="https://github.com/photo/openphoto-java">openphoto/openphoto-java</a> - Our Java language binding.
* More coming soon, <a href="mailto:hello@openphoto.me">contact us</a> if you'd like to write bindings in an unlisted language.

### Obtaining a consumer key and secret

Since Trovebox is distributed the flow to obtain a consumer key and secret differs slightly from typical OAuth applications.
Typically you would sign up for an application ID and be given a key and secret to be used with your app.
Trovebox differs because the host you'll be sending requests to is arbitrary and there's no central application repository.

### Resources on the web

If you're interested in learning more about OAuth then the following links are a great place to start.

* http://oauth.net/documentation/getting-started/
* http://hueniverse.com/oauth/guide/intro/
* http://www.slideshare.net/eran/introduction-to-oauth-presentation
