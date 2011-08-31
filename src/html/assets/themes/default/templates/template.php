<!doctype html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    
    <title><?php getTheme()->meta('titles', $page); ?></title>
    <meta name="description" content="<?php getTheme()->meta('descriptions', $page); ?>">
    <meta name="keywords" content="<?php getTheme()->meta('keywords', $page); ?>">

    
    <meta name="author" content="">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php getTheme()->asset('image', 'favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php getTheme()->asset('image', 'apple-touch-icon.png'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'html5_boilerplate_style.css'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'basics.css'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'header.css'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'pagination.css'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'photos.css'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'tag-cloud.css'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'upload.css'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'footer.css'); ?>">
</head>

<body class="<?php echo $page; ?>">

  <div id="container">
    <header>
      <?php getTheme()->display('partials/header.php'); ?>
    </header>

    <div id="main" role="main" class="wrapper">
      <!-- body -->
      <?php echo $body; ?>
    </div>
    <footer>
        <div class="wrapper"><a href="http://theopenphotoproject.org">The OpenPhoto Project</a> &copy; 2011</div>
    </footer>
  </div> <!-- eo #container -->
  <!-- your javascript here //-->
  <script type="text/javascript" src="<?php getTheme()->asset(getConfig()->get('dependencies')->javascript); ?>"></script>
  <script type="text/javascript" src="<?php getTheme()->asset('util'); ?>"></script>
  <script>
    OP.Util.init(jQuery, { 
      js: [
        '<?php getTheme()->asset('javascript', 'jquery.scrollTo-1.4.2-min.js'); ?>',
        '<?php getTheme()->asset('javascript', 'jquery.fileupload.min.js'); ?>',
        {src: '<?php getTheme()->asset('javascript', 'openphoto-theme.js'); ?>', callback: function(){ opTheme.init.attach(); }}
      ]
    });
  </script>
</body>
</html>
