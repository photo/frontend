# Contributing to OpenPhoto's Documentation

We're working hard to make OpenPhoto the best photo software possible, and part of that is making sure it's well-documented and accessible to beginners and experts alike. Writing good documentation is a big step toward that goal, and what you see here is the beginning of that effort.

We're always looking for writers to contribute to our documentation. If you can explain technical ideas clearly, we need you.

## Deciding what to contribute
We currently have documentation for the API and setting up OpenPhoto on a variety of servers (including shared hosting services), along with FAQs for the hosted and self-hosted sites. You can contribute by adding to these guides or by adding your own guide--for example, how to install OpenPhoto on your webhost. Your contributions will help users of the present and future who have the same questions.

All of this documentation is in the frontend repository under the Documentation folder. You can also contribute to the copy for theopenphotoproject.org, which is stored in <a href="https://github.com/photo/community/">the community repository</a>. We have <a href="https://github.com/photo/frontend/issues?labels=Documentation&page=1&state=open">issues tagged documentation on Github</a>, but every area of the project can benefit from better documentation. Choose something you know about and start writing.

## Forking the OpenPhoto repository
Before you start writing, you need to fork the repository you'll be working from. You can fork the repository right from the main OpenPhoto repository page by clicking the "Fork" button. If you plan on writing your documentation directly in Github, that's all you need to do. If you want to work from your local copy of the repository, <a href="https://help.github.com/articles/fork-a-repo">Github explains how to clone a repository</a>.

## Writing the documentation
We write our documentation in Github Standard Markdown and save the files as .Markdown files. If you're familiar with Markdown and Github, go ahead and fork OpenPhoto, then write your contribution in a text editor of your choice. If you're not, you may want to take a look at <a href="http://github.github.com/github-flavored-markdown/">Github's explanation of Markdown</a> as well as the source of OpenPhoto's documentation pages on Github.

A few things to keep in mind while writing the documentation:
* Remember that users of all skill levels will be reading the documentation. What you write should be accessible to all of them. This includes staying beginner- and expert-friendly, remaining gender-neutral, and being friendly and informative.
* Use correct grammar and spelling. <a href="https://owl.english.purdue.edu/owl/section/1/5/">Here's a grammar guide</a> if you need a refresher.
* Stay away from slang and other terms that don't translate well. This is for a couple for a reasons. First, OpenPhoto has an international userbase, and English may not be everyone's first language. Second, this makes translating easier when the time comes.

## Committing your documentation
When committing your documentation it's important to reference the GitHub issue you're fixing, if applicable. You can do it by adding a _#_ followed by the issue number.

    # To simply reference an issue with a commit do this
    git commit -m 'Addressing the foobar component but not yet finished. #123'
    
    # To commit and close an issue do this
    git commit -m 'Wrote the longest guide ever. Closes #123'

Be descriptive, it helps a ton. If you're working on the Github website you can do this in the commit summary at the bottom of the page you're writing in. Once you've committed your code it's time to push it to GitHub.

    git push origin master

## Getting your change into the main OpenPhoto branch
You can send your documentation to us by submitting a pull request. This way it can get reviewed and merged with the rest of the documentation. If you forked a copy of OpenPhoto to your local machine, <a href="http://help.github.com/send-pull-requests/">Github explains how to send a pull request with git.</a> If you wrote all the documentation through the Github website, you can press the Pull Request button that appears on the page containing your copy of the OpenPhoto repository.

## Help! I'm stuck and I have questions
If you have questions we're always around to help. We've got several contact options listed on the <a href="http://theopenphotoproject.org/contribute">contribute</a> page.
