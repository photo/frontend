<!doctype html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title><?php $this->theme->meta('titles', $page); ?></title>
    <meta name="description" content="<?php $this->theme->meta('descriptions', $page); ?>">
    <meta name="keywords" content="<?php $this->theme->meta('keywords', $page); ?>">


    <meta name="author" content="">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php $this->theme->asset('image', 'favicon.png'); ?>">
    <link rel="apple-touch-icon" href="<?php $this->theme->asset('image', 'apple-touch-icon.png'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'html5_boilerplate_style.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'basics.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'header.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'error.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'front.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'pagination.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'photos.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'tag-cloud.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'upload.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'footer.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'settings.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'setup.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'message-box.css'); ?>">
    <link rel="stylesheet" href="/assets/stylesheets/font-awesome-2.css">
    <link rel="stylesheet" href="/assets/stylesheets/upload.css">
    <?php $this->plugin->invoke('renderHead'); ?>
</head>

<body class="<?php echo $page; ?>">
  <div id="container">
    <header>
      <?php $this->theme->display('partials/header.php'); ?>
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
  <script type="text/javascript" src="<?php $this->theme->asset($this->config->dependencies->javascript); ?>"></script>
  <script type="text/javascript" src="<?php $this->theme->asset('util'); ?>"></script>
  <script>
    OP.Util.init(jQuery, {
      eventMap: {
        'click': {
            'action-box-click':'click:action-box',
            'action-delete-click':'click:action-delete',
            'action-jump-click':'click:action-jump',
            'action-post-click':'click:action-post',
            'credential-delete-click':'click:credential-delete',
            'group-checkbox-click':'click:group-checkbox',
            'group-update-click':'click:group-update',
            'login-click':'click:login',
            'map-jump-click':'click:map-jump',
            'nav-item-click':'click:nav-item',
            'pagination-click':'click:pagination',
            'photo-delete-click':'click:photo-delete',
            'photo-edit-click':'click:photo-edit',
            'photo-tag-click':'click:tag',
            'photo-thumbnail-click':'click:photo-thumbnail',
            'photo-update-click':'click:photo-update',
            'plugin-status-click':'click:plugin-status',
            'plugin-update-click':'click:plugin-update',
            'search-click':'click:search',
            'settings-click':'click:settings',
            'webhook-delete-click':'click:webhook-delete'
        },

        'keydown': {
            37: 'keydown:browse-previous',
            39: 'keydown:browse-next'
        }
      },
      js: {
        assets: [
          '<?php $this->theme->asset('javascript', 'jquery.scrollTo-1.4.2-min.js'); ?>',
          '<?php $this->theme->asset('javascript', 'jquery.fileupload.min.js'); ?>',
          '<?php $this->theme->asset('javascript', 'jquery.cycle.min.js '); ?>',
          '/assets/javascripts/openphoto-upload.min.js',
          '<?php $this->theme->asset('javascript', 'openphoto-theme.js'); ?>'
        ],
        onComplete: function(){ opTheme.init.attach(); }
      }
    });
  </script>
  <?php $this->plugin->invoke('renderFooter'); ?>
</body>
</html>
