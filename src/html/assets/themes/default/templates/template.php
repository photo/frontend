<!doctype html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    
    <title></title>
    <meta name="description" content="">

    
    <meta name="author" content="">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="stylesheet" href="css/html5_boilerplate_style.css">
    <link rel="stylesheet" href="css/basics.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/pagination.css">
    <link rel="stylesheet" href="css/photos.css">
    <link rel="stylesheet" href="css/tag-cloud.css">
    <link rel="stylesheet" href="css/footer.css">
</head>

<body class="photos">

  <div id="container">
    <header>
        <div class="wrapper">
            <h1>OpenPhoto</h1>
            <nav>
                <ul>
                    <!--<li class="home "><a href="/"><img src="/assets/img/default/header-navigation-home.png" align="absmiddle">Home</a></li>-->
                    <li id="nav-photos" <?php Utility::isActiveTag('photos') { ?> class="on" <?php } ?>>
                        <a href="/photos">Photos</a>
                    </li>
                    <li id="nav-tags" <?php Utility::isActiveTag('tags') { ?> class="on" <?php } ?>>
                        <a href="/tags">Tags</a>
                    </li>
                    <li id="nav-search" <?php Utility::isActiveTag('search') { ?> class="on" <?php } ?>>
                        <a id="search-bar-toggle" role="button">Search</a>
                        <div id="searchbar" class="offscreen">
                            <form method="get" id="form-tag-search">
                                <input type="text" name="tags" placeholder="Enter a tag" class="select"><button type="submit">Search</button>
                            </form>
                        </div>
                    </li>
                    <?php if(User::isOwner()) { ?>
                      <li id="nav-upload" <?php Utility::isActiveTag('search') { ?> class="on" <?php } ?>>
                        <a href="/upload">Upload</a>
                      </li>
                    <?php } ?>
                    <?php if(User::isLoggedIn()) { ?>
                      <li id="nav-signin">
                        <?php echo getSession()->get('email'); ?><img src="/assets/img/default/header-navigation-user.png" align="absmiddle">
                      </li>
                    <?php } else { ?>
                      <li id="nav-signin">
                          <button class="login"><img src="https://browserid.org/i/sign_in_blue.png" alt="Signin to OpenPhoto"></button>
                          <!--<a href="#" class="login"><img src="https://browserid.org/i/sign_in_blue.png" align="absmiddle"></a>-->
                      </li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </header>

    <div id="main" role="main" class="wrapper">
      <!-- body -->
    </div>
    <footer>
        <div class="wrapper"><a href="http://theopenphotoproject.org">The OpenPhoto Project</a> &copy; 2011</div>
    </footer>
  </div> <!-- eo #container -->
  <!-- your javascript here //-->
</body>
</html>
