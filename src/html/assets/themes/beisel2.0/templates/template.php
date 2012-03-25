<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

		<meta name="language" content="english,en" />
		<meta name="distribution" content="global" />
		<meta name="robots" content="index,follow" />
		<meta name="revisit-after" content="7 days" />
    <meta name="description" content="<?php $this->theme->meta('descriptions', $page); ?>">
    <meta name="keywords" content="<?php $this->theme->meta('keywords', $page); ?>">
    <meta name="author" content="The OpenPhoto Project (http://theopenphotoproject.org)">
		<!--<meta name="publisher" content="openphoto" />
		<meta name="copyright" content="openphoto" />-->

		<!--[if lt IE 9]>
		  <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
    <link rel="shortcut icon" href="<?php $this->theme->asset('image', 'favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php $this->theme->asset('image', 'apple-touch-icon.png'); ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php $this->theme->asset('image', 'apple-touch-icon-72x72.png'); ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php $this->theme->asset('image', 'apple-touch-icon-114x114.png'); ?>">

    <?php if($this->config->site->mode === 'dev') { ?>
      <link href="<?php $this->theme->asset('stylesheet', 'bootstrap.min.css'); ?>" rel="stylesheet">
      <link href="<?php $this->theme->asset('stylesheet', 'opme.css'); ?>" rel="stylesheet">
      <?php if($this->user->isOwner()) { ?>
        <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'owner.css'); ?>">
      <?php } ?>
    <?php } else { ?>
      <link rel="stylesheet" href="<?php echo getAssetPipeline(true)->addCss($this->theme->asset('stylesheet', 'bootstrap.min.css', false))->
                                                                  addCss("/assets/stylesheets/upload.css")->
                                                                  addCss($this->theme->asset('stylesheet', 'main.css', false))->
                                                                  getUrl(AssetPipeline::css, 'h'); ?>">
      <?php if($this->user->isOwner()) { ?>
        <link rel="stylesheet" href="<?php echo getAssetPipeline(true)->addCss($this->theme->asset('stylesheet', 'owner.css', false))->
                                                                  getUrl(AssetPipeline::css, 'c'); ?>">
      <?php } ?>
    <?php } ?>

    <?php if(!$this->plugin->isActive('BetterPageTitles')) { ?>
      <title><?php $this->theme->meta('titles', $page); ?></title>
    <?php } ?>
    <?php $this->plugin->invoke('renderHead'); ?>
	</head>

  <body class="<?php echo $page; ?>">
    <script>document.body.className += ' js';</script>
    <?php $this->theme->display('partials/navigation.php'); ?>

		<div class="container">	
      <?php $this->plugin->invoke('renderBody'); ?>
      <div class="message"></div>

      <div class="content">
        <?php echo $body; ?>
      </div>
      
      <div class="modal hide fade" id="modal"></div>
      <div class="modal-photo-detail hide fade span12" id="modal-photo-detail"></div>
      <?php if(!$this->user->isLoggedIn()) { ?>
        <?php $this->theme->display('partials/login.php'); ?>
      <?php } ?>

			<footer>
        <p>&copy; <?php echo date('Y'); ?> <a href="http://theopenphotoproject.org">The OpenPhoto Project</a></p>
			</footer>

		</div>

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
              'group-delete-click':'click:group-delete',
              'group-email-add-click':'click:group-email-add',
              'group-email-remove-click':'click:group-email-remove',
              'group-post-click':'click:group-post',
              'login-modal-click':'click:login-modal',
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
              'photo-view-click':'click:photo-view',
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
              '<?php $this->theme->asset('javascript', 'jquery.history.js'); ?>',
              '<?php $this->theme->asset('javascript', 'bootstrap.min.js'); ?>',
              '<?php $this->theme->asset('javascript', 'bootstrap-modal.js'); ?>',
              '<?php $this->theme->asset('javascript', 'touchSwipe.js'); ?>',
              '<?php $this->theme->asset('javascript', 'browserupdate.js'); ?>',
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
          }
        }
      });
    </script>
    <?php $this->plugin->invoke('renderFooter'); ?>
  </body>
</html>
