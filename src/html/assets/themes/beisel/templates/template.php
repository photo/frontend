<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="english,en" />
	<meta name="distribution" content="global" />
    <title><?php getTheme()->meta('titles', $page); ?></title>
    <meta name="description" content="<?php getTheme()->meta('descriptions', $page); ?>">
    <meta name="keywords" content="<?php getTheme()->meta('keywords', $page); ?>">
    <meta name="author" content="openphoto.me">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php getTheme()->asset('image', 'favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php getTheme()->asset('image', 'apple-touch-icon.png'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'main.css'); ?>">
</head>

<body class="<?php echo $page; ?>">

	<div id="wrapper">

		<header>
		  <?php getTheme()->display('partials/header.php'); ?>
		</header>

		<article id="main" role="main">
		  <!-- body -->
		  <?php echo $body; ?>
		</article>

		<footer>
		    <a href="http://theopenphotoproject.org" title="Learn more about the OpenPhoto project">The OpenPhoto Project</a> &copy; 2011
		</footer>

	</div>
	<script type="text/javascript" src="<?php getTheme()->asset(getConfig()->get('dependencies')->javascript); ?>"></script>
	<script type="text/javascript" src="<?php getTheme()->asset('util'); ?>"></script>
	<script>
	  OP.Util.init(jQuery, {
	    js: {
	      assets: [
	        '<?php getTheme()->asset('javascript', 'jquery.scrollTo-1.4.2-min.js'); ?>',
	        '<?php getTheme()->asset('javascript', 'jquery.fileupload.min.js'); ?>', 
	        '<?php getTheme()->asset('javascript', 'jquery.cycle.min.js '); ?>', 
	        '<?php getTheme()->asset('javascript', 'openphoto-theme.js'); ?>',
	        '<?php getTheme()->asset('javascript', 'jquery.flexslider-min.js'); ?>'
	      ],
        onComplete: function(){ 
          opTheme.init.attach(); 
          if($("section#slideshow").length > 0) {
            $(window).load(function() {
              $('.flexslider').flexslider({
                animation: "slide",
                controlsContainer: ".flex-container",
                controlNav: true,
                pausePlay: false,
                directionNav: true,
                nextText: "<span title='Next'>Next</span>",
                prevText: "<span title='Previous'>Previous</span>"
              });
            });
          }
        }
	    }
	  });
	</script>
</body>
</html>
