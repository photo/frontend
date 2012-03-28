<!doctype html>
<html class="no-js" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="language" content="english,en" />
  <meta name="distribution" content="global" />
    <title><?php $this->theme->meta('titles', $page); ?></title>
    <meta name="description" content="<?php $this->theme->meta('descriptions', $page); ?>">
    <meta name="keywords" content="<?php $this->theme->meta('keywords', $page); ?>">
    <meta name="author" content="openphoto.me">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php $this->theme->asset('image', 'favicon.png'); ?>">
    <link rel="apple-touch-icon" href="<?php $this->theme->asset('image', 'apple-touch-icon.png'); ?>">
    <?php if($this->config->site->mode === 'dev') { ?>
      <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'bootstrap.min.css'); ?>">
      <link rel="stylesheet" href="/assets/stylesheets/upload.css">
      <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'main.css'); ?>">
      <?php if(true || $this->user->isOwner()) { ?>
        <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'owner.css'); ?>">
      <?php } ?>
    <?php } else { ?>
      <link rel="stylesheet" href="<?php echo getAssetPipeline(true)->addCss($this->theme->asset('stylesheet', 'bootstrap.min.css', false))->
                                                                  addCss("/assets/stylesheets/upload.css")->
                                                                  addCss($this->theme->asset('stylesheet', 'main.css', false))->
                                                                  getUrl(AssetPipeline::css, 'i'); ?>">
      <?php if(true || $this->user->isOwner()) { ?>
        <link rel="stylesheet" href="<?php echo getAssetPipeline(true)->addCss($this->theme->asset('stylesheet', 'owner.css', false))->
                                                                  getUrl(AssetPipeline::css, 'd'); ?>">
      <?php } ?>
    <?php } ?>

    <?php $this->plugin->invoke('renderHead'); ?>
</head>

<body class="<?php echo $page; ?>">
  <div id="wrapper" class="container">

    <div class="row">
      <header>
        <?php $this->theme->display('partials/header.php'); ?>
      </header>
    </div>
    
    <div id="message"></div>

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
  <?php if($this->config->site->mode === 'dev') { ?>
    <script type="text/javascript" src="<?php $this->theme->asset($this->config->dependencies->javascript); ?>"></script>
    <script type="text/javascript" src="<?php $this->theme->asset('util'); ?>"></script>
  <?php } else { ?>
    <script type="text/javascript" src="<?php echo getAssetPipeline(true)->addJs($this->theme->asset($this->config->dependencies->javascript, null, false))->
                                                                      addJs($this->theme->asset('util', null, false))->getUrl(AssetPipeline::js, 'd'); ?>"></script>
  <?php } ?>
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
            'pin-click':'click:pin',
            'pin-clear-click':'click:pin-clear',
            'plugin-status-click':'click:plugin-status',
            'plugin-update-click':'click:plugin-update',
            'search-click':'click:search',
            'settings-click':'click:settings',
            'upload-start-click':'click:upload-start',
            'webhook-delete-click':'click:webhook-delete'
        },
        <?php if($this->user->isOwner()) { ?>
          'change': {
              'batch-field-change':'change:batch-field'
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
            <?php if($this->config->site->mode === 'dev') { ?>
              '/assets/javascripts/plupload.js',
              '/assets/javascripts/plupload.html5.js',
              '/assets/javascripts/jquery.plupload.queue.js',
              '/assets/javascripts/openphoto-upload.js',
            <?php } else { ?>
              '<?php echo getAssetPipeline(true)->addJs('/assets/javascripts/openphoto-upload.min.js')->getUrl(AssetPipeline::js, 'f'); ?>',
            <?php } ?>
          <?php } ?>

          <?php if($this->config->site->mode === 'dev') { ?>
            '/assets/javascripts/openphoto-batch.js',
            '<?php $this->theme->asset('javascript', 'jquery.scrollTo-1.4.2-min.js'); ?>',
            '<?php $this->theme->asset('javascript', 'jquery.flexslider-min.js'); ?>',
            '<?php $this->theme->asset('javascript', 'jquery.tokeninput.js'); ?>',
            '<?php $this->theme->asset('javascript', 'bootstrap-modal.js'); ?>',
            '<?php $this->theme->asset('javascript', 'openphoto-theme.js'); ?>'
          <?php } else { ?>
            '<?php echo getAssetPipeline(true)->addJs('/assets/javascripts/openphoto-batch.min.js')->
                                                addJs($this->theme->asset('javascript', 'openphoto-theme-full-min.js', false))->
                                                getUrl(AssetPipeline::js, 'i'); ?>'
          <?php } ?>
        ],
        onComplete: function(){ 
          opTheme.init.load('<?php $this->utility->safe($this->session->get('crumb')); ?>'); 
          opTheme.init.attach(); 
          <?php if(isset($_GET['__route__']) && strstr($_GET['__route__'], 'photo') !== false) { ?>
            opTheme.init.photos(); 
          <?php } ?>
        }
      }
    });
  </script>
  <?php $this->plugin->invoke('renderFooter'); ?>
</body>
</html>
