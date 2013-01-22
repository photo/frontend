<!doctype html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title><?php getTheme()->meta('titles', $page); ?></title>
    <meta name="description" content="<?php getTheme()->meta('descriptions', $page); ?>">
    <meta name="keywords" content="<?php getTheme()->meta('keywords', $page); ?>">


    <meta name="author" content="">

    <meta name="viewport" content="width=device-width; initial-scale = 1.0; maximum-scale=1.0;" />
    <link rel="shortcut icon" href="<?php getTheme()->asset('image', 'favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php getTheme()->asset('image', 'apple-touch-icon.png'); ?>">
    <link rel="stylesheet" href="/assets/stylesheets/html5_boilerplate_style.css">
    <link rel="stylesheet" href="/assets/stylesheets/basics.css">
    <link rel="stylesheet" href="/assets/stylesheets/main.css" media="screen and (min-device-width: 481px)">
    <link rel="stylesheet" href="/assets/stylesheets/mobile.css" media="screen and (max-device-width: 480px)">
  </head>
<body class="<?php echo $page; ?>">
  <header>
    <h1><div class="offscreen">Trovebox</div></h1>
  </header>
  <div class="container">
    <?php echo $body; ?>
  </div>
</body>
</html>
