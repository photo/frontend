Contributing to the Web and API components
=======================

## Up and contributing in under 5 minutes

So what do you need to start contributing?

A computer! Once you've secured one of those all you need is a Github account. It helps to have a locally installed version of the frontend repository to test your changes. We've got <a href="http://theopenphotoproject.org/documentation">lots of guides</a> to help you get started. If you're updating any of the PHP code, then you should get PHPUnit as well.

    pear channel-discover pear.phpunit.de
    pear install pear.phpunit.de/PHPUnit
    
    # problems? check the links below
    # http://www.phpunit.de/manual/3.6/en/installation.html
    # http://stackoverflow.com/questions/3301300/setting-up-phpunit-on-osx
    
Now that you've got the following...

* A computer
* <a href="https://github.com">A GitHub account</a>
* An <a href="http://theopenphotoproject.org/documentation">installation of the frontend repository</a>
* <a href="http://www.phpunit.de/manual/3.6/en/installation.html">PHPUnit</a>

...we can continue.

## Deciding what to fix

We've added a _Beginner_ label to issues that don't touch some of the more sensitive parts of the code. We've additionally added _CSS_, _JavaScript_, and _PHP_ labels, so you can narrow it down to exactly what you're interested in.

* <a href="https://github.com/openphoto/frontend/issues?labels=Beginner&sort=created&direction=desc&state=open&page=1">All Beginner issues</a>
* <a href="https://github.com/openphoto/frontend/issues?labels=Beginner%2CCSS&sort=created&direction=desc&state=open&page=1">Beginner + CSS issues</a>
* <a href="https://github.com/openphoto/frontend/issues?labels=Beginner%2CJavaScript&sort=created&direction=desc&state=open&page=1">Beginner + JavaScript issues</a>
* <a href="https://github.com/openphoto/frontend/issues?labels=Beginner%2CPHP&sort=created&direction=desc&state=open&page=1">Beginner + PHP issues</a>

## Testing that your change didn't break anything

Once you've made your change, it can be verified in order to make sure it's doing what it should and not something it shouldn't. This is as easy as a single command.

    phpunit src/tests
    ............................................................  60 / 311
    ............................................................ 120 / 311
    ............................................................ 180 / 311
    ...............I....................................I....... 240 / 311
    ........I......................I............................ 300 / 311
    ...........

    Time: 2 seconds, Memory: 16.50Mb

    OK, but incomplete or skipped tests!
    Tests: 311, Assertions: 661, Incomplete: 4.

Those `I`s are okay but you shouldn't see any `E`s or `F`s and definitely look for the _OK_ message at the end. If all the tests pass then you're good to go and can commit it.

## Committing your code

When committing your code it's important to reference the GitHub issue you're fixing. You can do it by adding a _#_ followed by the issue number.

    # To simply reference an issue with a commit do this
    git commit -m 'Addressing the foobar component but not yet finished. #123'
    
    # To commit and close an issue do this
    git commit -m 'Fixing the most annoying bug ever. Closes #123'

Be descriptive! It helps a ton. Once you've committed your code it's time to push it to GitHub.

    git push origin master

## Getting your change into the OpenPhoto branch

To get your change merged into the official OpenPhoto branch you should submit a pull request. <a href="http://help.github.com/send-pull-requests/">GitHub's tutorial</a> is better than anything we could do, so we'll link to it.

It makes everyone's life easier if you can remember to issue the pull request to OpenPhoto's development. If you forget, no big deal! The important thing is we get your change! It is then that your awesomeness can be merged into everyone else's awesomeness.

## Help! I'm stuck and have questions

If you have questions we're always around to help. We've got several contact options listed on the <a href="http://theopenphotoproject.org/contribute">contribute</a> page.
