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
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'jquery.mobile.css'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'photoswipe.css'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'mobile.css'); ?>">
</head>
<body class="<?php echo $page; ?>">
    <?php echo $body; ?>
    <script type="text/javascript" src="<?php getTheme()->asset(getConfig()->get('dependencies')->javascript); ?>"></script>
    <script type="text/javascript" src="<?php getTheme()->asset('util'); ?>"></script>
    <script type="text/javascript" src="<?php getTheme()->asset('javascript', 'jquery.mobile.js'); ?>"></script>
    <script type="text/javascript" src="<?php getTheme()->asset('javascript', 'ps.js'); ?>"></script>
    <script>
      /*
       * IMPORTANT!!!
       * REMEMBER TO ADD  rel="external"  to your anchor tags. 
       * If you don't this will mess with how jQuery Mobile works
       */
      
      (function(window, $, PhotoSwipe){
        console.log('yes');
        $(document).ready(function(){
        console.log('ready');
          $('div.gallery-page').live('pageshow', function(e){
        console.log('show');
              
            // See if there is a PhotoSwipe instance associated with the page.
            // For this demo I've assumed one page has one instance and the ID 
            // for each instance is the same as the page ID.
            //
            // Of course, it's up to you how many instances per page and what
            // ID naming convention you use!
            var 
              currentPage = $(e.target),
              photoSwipeInstanceId = currentPage.attr('id'),
              photoSwipeInstance = PhotoSwipe.getInstance(photoSwipeInstanceId)
              options = {};
            
            if (typeof photoSwipeInstance === "undefined" || photoSwipeInstance === null) {
              photoSwipeInstance = $("ul.gallery a", e.target).photoSwipe(options, photoSwipeInstanceId);
            }
            
            return true;
            
          });
            
        });
      
      }(window, window.jQuery, window.Code.PhotoSwipe));
    </script>
    <!--<script>
        OP.Util.init(jQuery, {
          js: {
            assets: [
              '<?php getTheme()->asset('javascript', 'jquery.mobile.js'); ?>',
              '<?php getTheme()->asset('javascript', 'ps.js'); ?>'
            ],
            onComplete: function(){
              $('div.gallery-page').live('pageshow', function(e){
                  
                // See if there is a PhotoSwipe instance associated with the page.
                // For this demo I've assumed one page has one instance and the ID 
                // for each instance is the same as the page ID.
                //
                // Of course, it's up to you how many instances per page and what
                // ID naming convention you use!
                var 
                  currentPage = $(e.target),
                  photoSwipeInstanceId = currentPage.attr('id'),
                  photoSwipeInstance = PhotoSwipe.getInstance(photoSwipeInstanceId)
                  options = {};
                
                if (typeof photoSwipeInstance === "undefined" || photoSwipeInstance === null) {
                  photoSwipeInstance = $("ul.gallery a", e.target).photoSwipe(options, photoSwipeInstanceId);
                }
                return true;
              });
            }
          }
        });
    </script>-->
</body>
</html>
