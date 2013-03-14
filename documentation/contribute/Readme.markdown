Contributing to The Photo Project
=======================

## TL;DR
Alright, we get it. You'd rather code than read. Here's the checklist.

1. Fork the <a href="https://github.com/photo/frontend">frontend</a> repository. <a href="http://help.github.com/fork-a-repo/">More info on forking</a>.
1. Follow one of our <a href="/documentation">guides</a> on setting the software up.
1. Get coding!
1. <a href="/contribute/frontend">More details</a> if you need them.

<div></div>

    # replace {username} with your github username
    # clone your repository locally and cd into the new directory
    user@ git clone git@github.com:{username}/frontend.git 
    user@ cd frontend

    # add the official openphoto repository as upstream 
    user@[frontend] git remote add upstream git://github.com/photo/frontend.git
    user@[frontend] git pull upstream
    
    # all active development happens in the development branch
    # as a result it maybe unstable so you can always use master which should work
    user@[frontend] git checkout development

    # make your changes and commit them locally
    user@[frontend] git commit -m 'Lots of details! Closes #123'
    user@[frontend] git push origin development

Now you're ready to <a href="http://help.github.com/send-pull-requests/">send a pull request</a>!

## There's a spot for everyone to be a part of something BIG

Hear ye, hear ye! <a href="#developers">Developers</a>, <a href="#designers">designers</a>, <a name="#powerusers">power users</a>, <a href="#copywriters">copywriters</a>, <a href="#community">community managers</a>, <a href="#translators">translators</a>, and anyone else who wants to help. If you want to contribute to The OpenPhoto Project, the good news is that there's probably a place where we could use your help!

It's easy to get in touch with us, as many of us hang out in #openphoto on Freenode. Come in and <a href="http://webchat.freenode.net/">chat with us</a>. We're happy to answer any questions you might have.

Here's a full list of ways to contact us:

* <a href="http://webchat.freenode.net/">Chat with us on IRC #openphoto on freenode.net</a>
* <a href="http://groups.google.com/group/openphoto">Join our mailing list on Google Groups</a>
* <a href="https://github.com/photo">Submit bugs on Github</a>
   * <a href="https://github.com/photo/frontend">Web or API</a>
   * <a href="https://github.com/photo/mobile-ios">iPhone app</a>
   * <a href="https://github.com/photo/mobile-android">Android app</a>
   * <a href="https://github.com/photo/openphoto-php">PHP bindings</a>
   * <a href="https://github.com/photo/openphoto-ruby">Ruby bindings</a>
   * <a href="https://github.com/photo/openphoto-python">Python bindings</a>
   * <a href="https://github.com/photo/openphoto-java">Java bindings</a>
* <a href="http://twitter.com/photo">Follow us on Twitter</a>
* <a href="http://www.facebook.com/OpenPhoto">Like us on Facebook</a>

<a name="developers"></a>
## Developers and Designers

There's a lot of engineering goodness to be had here. We've got code written in HTML, CSS, JavaScript, PHP, Ruby, Python, Java and Objective-C. If you're interested helping out with a specific part of OpenPhoto, then fork the repository and send us some pull requests. If you don't know exactly where to begin, then contact us. We're more than happy to help you get started.

That being said, the largest part of the code base is the <a href="https://github.com/photo/frontend">frontend</a> repository. It houses the web interface, as well as the REST API which every other repository communicates to. This repository is also the one we've got a formal process to contribute to with unit tests and the works.

<a href="http://theopenphotoproject.org/contribute/frontend" class="btn danger">Start contributing</a>

<a name="copywriters"></a>
## Copywriters and Wordsmiths

If you've got a gift with words we're in desperate need of your help. Much of the community consists of engineers and well, we suck at taking complex ideas and distilling them into small and easy to understand sentences.

The best way to get started is to read the copy at <a href="http://theopenphotoproject.org">The OpenPhoto Project</a>'s website. Some of it is technical, like the documentation, but much of it is also trying to explain why users should care about what we're building. Wherever your passion lies, we will definitely appreciate the help.

Everything can be found in the <a href="https://github.com/photo/community">community</a> repository for the non-technical bits or the <a href="https://github.com/photo/frontend">frontend</a> repository for the documentation.

We have <a href="https://github.com/photo/frontend/issues?labels=Documentation&page=1&state=open">issues tagged documentation</a> on Github, but just about every area of the site needs some attention. You can open an issue for the appropriate repository or fork it and send us a pull request. If you have any questions, let us know using one of the channels above.

<a name="powerusers"></a>
## Power Users
Do you know OpenPhoto inside out, including all its little quirks? Then we need you! A lot of people ask questions about OpenPhoto on our mailing list or on IRC, so hang out on those channels and help someone out. This is a great way to get to know other OpenPhoto users. You can also help out by filing Github issues when you find bugs and discussing them on our issues board.

<a name="community"></a>
## Community Managers and Social Media

Send an email to <a href="mailto:hello@openphoto.me">hello@openphoto.me</a> or <a href="https://twitter.com/openphoto">@mention us</a> on Twitter if you'd like to help with Community Management or Social Media.

<a name="translators"></a>
## Translators

We want to make The Photo Project available for everyone. This means translating it into as many languages as possible. If you're interested in helping us out, then drop an email to <a href="mailto:hello@openphoto.me">hello@openphoto.me</a> and we'll be in touch once we have our translation platform ready.
