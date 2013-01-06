Contributing to the Web and API components
=======================

## Up and contributing in under 5 minutes

So what do you need to get started?

A computer! Once you've secured one of those all you need is a Github account. It helps to have a locally installed version of the frontend repository to test your changes. We've got <a href="http://theopenphotoproject.org/documentation">lots of guides</a> to help you get started. If you're updating any of the PHP code you should get PHPUnit as well.

    pear channel-discover pear.phpunit.de
    pear channel-discover pear.bovigo.org
    pear install pear.phpunit.de/PHPUnit
    pear install bovigo/vfsStream-beta
    
    # problems? check the links below
    # http://www.phpunit.de/manual/3.6/en/installation.html
    # http://stackoverflow.com/questions/3301300/setting-up-phpunit-on-osx
    
Now that you've got:

* A computer
* <a href="https://github.com">A GitHub account</a>
* An <a href="http://theopenphotoproject.org/documentation">installation of the frontend repository</a>
* <a href="http://www.phpunit.de/manual/3.6/en/installation.html">PHPUnit</a>,

let's continue.

## Deciding what to fix

We've added a _Beginner_ label to issues that don't touch some of the more sensitive parts of the code. We've additionally added _CSS_, _JavaScript_, and _PHP_ labels so you can narrow it down to exactly what you're interested in.

* <a href="https://github.com/photo/frontend/issues?labels=Beginner&sort=created&direction=desc&state=open&page=1">All Beginner issues</a>
* <a href="https://github.com/photo/frontend/issues?labels=Beginner%2CCSS&sort=created&direction=desc&state=open&page=1">Beginner + CSS issues</a>
* <a href="https://github.com/photo/frontend/issues?labels=Beginner%2CJavaScript&sort=created&direction=desc&state=open&page=1">Beginner + JavaScript issues</a>
* <a href="https://github.com/photo/frontend/issues?labels=Beginner%2CPHP&sort=created&direction=desc&state=open&page=1">Beginner + PHP issues</a>

## Things to keep in mind while you code
Here's what your code should adhere to:

* Unit tests should pass (more on that in the next section)
* Spacing matters: two spaces, no tabs
* Commits should reference an issue number (more on that below)
* Comment your code so future developers can tell what's going on
* Curly braces go on their own line. For example:
````php
  if(condition)
  {
      statement 1;
      statement 2;
  }

  // or
  if(condition)
      only statement;
````
All in all, we recognize that everyone has a different style and level of experience, and we welcome all pull requests.

## Testing that your change didn't break anything

Once you've made your change and verified it does what it should it's time to make sure it's not doing something it shouldn't. This is as easy as a single command.

    phpunit -c src/tests/phpunit.xml
    ............................................................  60 / 311
    ............................................................ 120 / 311
    ............................................................ 180 / 311
    ...............I....................................I....... 240 / 311
    ........I......................I............................ 300 / 311
    ...........

    Time: 2 seconds, Memory: 16.50Mb

    OK, but incomplete or skipped tests!
    Tests: 311, Assertions: 661, Incomplete: 4.

Those `I`s are okay but you shouldn't see any `E`s or `F`s and definitely look for the `OK` message at the end. If all the tests pass then you're good to go and can commit your changes.

You can automate this by adding a pre-commit hook. Just copy <a href="https://github.com/photo/frontend/blob/master/documentation/hooks/pre-commit">this file</a> into your `.git/hooks` directory or run the command below.

    wget --no-check-certificate https://raw.github.com/photo/frontend/master/documentation/hooks/pre-commit -O .git/hooks/pre-commit
    chmod u+x .git/hooks/pre-commit

Now every time you make a commit it will first run the unit tests automatically.

## Committing your code

When committing your code it's important to reference the GitHub issue you're fixing. You can do it by adding a _#_ followed by the issue number.

    # To simply reference an issue with a commit do this
    git commit -m 'Addressing the foobar component but not yet finished. #123'
    
    # To commit and close an issue do this
    git commit -m 'Fixing the most annoying bug ever. Closes #123'

Be descriptive, it helps a ton. Once you've committed your code it's time to push it to GitHub.

    git push origin master

## Getting your change into the OpenPhoto branch

To get your change merged into the official OpenPhoto branch, submit a pull request. <a href="http://help.github.com/send-pull-requests/">GitHub's tutorial</a> is better than anything we could do so we'll link to it.

It makes everyone's lives easier if you can remember to issue the pull request to OpenPhoto's development branch. If you forget, no big deal. The important thing is we get your change and your awesomeness can be merged into everyone else's awesomeness.

## Help! I'm stuck and have questions

If you have questions we're always around to help. We've got several contact options listed on the <a href="http://theopenphotoproject.org/contribute">contribute</a> page.
