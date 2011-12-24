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
    <link rel="shortcut icon" href="<?php getTheme()->asset('image', 'favicon.png'); ?>">
    <link rel="apple-touch-icon" href="<?php getTheme()->asset('image', 'apple-touch-icon.png'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php getTheme()->asset('stylesheet', 'main.css'); ?>">
    <link rel="stylesheet" href="/assets/stylesheets/upload.css">
    <?php getPlugin()->invoke('onHead', array('page' => $page)); ?>
</head>

<body class="<?php echo $page; ?>">
  <?php getPlugin()->invoke('onBodyBegin', array('page' => $page)); ?>

  <div id="wrapper" class="container">

    <div class="row">
      <header>
        <?php getTheme()->display('partials/header.php'); ?>
      </header>
    </div>
    
    <div id="message" class="row"></div>

    <div class="row">
      <article id="main" role="main">
        <!-- body -->
        <?php echo $body; ?>
      </article>
    </div>

    <div class="row">
      <div class="span16">
        <footer>
          <a href="http://theopenphotoproject.org" title="Learn more about the OpenPhoto project">The OpenPhoto Project</a> &copy; 2011
        </footer>
      </div>
    </div>
    
  </div>
  <div id="modal" class="modal hide fade"></div>
  <script type="text/javascript" src="<?php getTheme()->asset(getConfig()->get('dependencies')->javascript); ?>"></script>
  <script type="text/javascript" src="<?php getTheme()->asset('util'); ?>"></script>
  <script type="text/javascript" src="/assets/javascripts/openphoto-batch.js"></script>
  <script>
    OP.Util.init(jQuery, {
      eventMap: {
        'click': {
            'action-box-click':'click:action-box',
            'action-delete-click':'click:action-delete',
            'action-jump-click':'click:action-jump',
            'action-post-click':'click:action-post',
            'batch-modal-click':'click:batch-modal',
            'credential-delete-click':'click:credential-delete',
            'group-checkbox-click':'click:group-checkbox',
            'group-update-click':'click:group-update',
            'login-click':'click:login',
            'map-jump-click':'click:map-jump',
            'modal-close-click':'click:modal-close',
            'nav-item-click':'click:nav-item',
            'pagination-click':'click:pagination',
            'photo-delete-click':'click:photo-delete',
            'photo-edit-click':'click:photo-edit',
            'photo-tag-click':'click:tag',
            'photo-thumbnail-click':'click:photo-thumbnail',
            'photo-update-click':'click:photo-update',
            'photo-update-batch-click':'click:photo-update-batch',
            'plugin-status-click':'click:plugin-status',
            'plugin-update-click':'click:plugin-update',
            'search-click':'click:search',
            'settings-click':'click:settings',
            'webhook-delete-click':'click:webhook-delete',
            'pin-click':'click:pin',
            'pin-clear-click':'click:pin-clear'
        },
        <?php if(User::isOwner()) { ?>
          'mouseover': {
              'pin-over':'mouseover:pin',
              'pin-out':'mouseout:pin',
          },
        <?php } ?>
        'keydown': {
            37: 'keydown:browse-previous',
            39: 'keydown:browse-next'
        },
      },
      js: {
        assets: [
          <?php if(isset($_GET['__route__']) && stristr($_GET['__route__'], 'upload')) { ?> 
            <?php if(isset($_GET['debug'])) { ?>
              '<?php getTheme()->asset('javascript', 'plupload.js'); ?>',
              '<?php getTheme()->asset('javascript', 'plupload.html5.js'); ?>',
              '<?php getTheme()->asset('javascript', 'jquery.plupload.queue.js'); ?>',
              '/assets/javascripts/openphoto-upload.js',
            <?php } else { ?>
              '/assets/javascripts/openphoto-upload.min.js',
            <?php } ?>
          <?php } ?>

          <?php if(isset($_GET['debug'])) { ?>
            '<?php getTheme()->asset('javascript', 'jquery.scrollTo-1.4.2-min.js'); ?>',
            '<?php getTheme()->asset('javascript', 'jquery.flexslider-min.js'); ?>',
            '<?php getTheme()->asset('javascript', 'bootstrap-modal.js'); ?>',
            '<?php getTheme()->asset('javascript', 'openphoto-theme.js'); ?>'
          <?php } else { ?>
            '<?php getTheme()->asset('javascript', 'openphoto-theme-full-min.js'); ?>'
          <?php } ?>
        ],
        onComplete: function(){ 
          opTheme.init.load('<?php Utility::safe(getSession()->get('crumb')); ?>'); 
          opTheme.init.attach(); 
          <?php if(isset($_GET['__route__']) && strstr($_GET['__route__'], 'photo') !== false) { ?>
            opTheme.init.photos(); 
          <?php } ?>
        }
      }
    });
  </script>
  <?php getPlugin()->invoke('onBodyEnd', array('page' => $page)); ?>
</body>
</html>
