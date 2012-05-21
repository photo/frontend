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
    <link rel="shortcut icon" href="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php $this->theme->asset('image', 'favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php $this->theme->asset('image', 'apple-touch-icon.png'); ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php $this->theme->asset('image', 'apple-touch-icon-72x72.png'); ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php $this->theme->asset('image', 'apple-touch-icon-114x114.png'); ?>">

    <?php if($this->config->site->mode === 'dev') { ?>
      <link href="<?php $this->theme->asset('stylesheet', 'bootstrap.min.css'); ?>" rel="stylesheet">
      <link href="<?php $this->theme->asset('stylesheet', 'opme.css'); ?>" rel="stylesheet">
      <link href="/assets/stylesheets/upload.css" rel="stylesheet">
    <?php } else { ?>
      <link rel="stylesheet" href="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->addCss($this->theme->asset('stylesheet', 'bootstrap.min.css', false))->
                                                                  addCss("/assets/stylesheets/upload.css")->
                                                                  addCss($this->theme->asset('stylesheet', 'opme.css', false))->
                                                                  getUrl(AssetPipeline::css, 'o'); ?>">
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
      <div class="modal photo-detail hide fade" id="modal-photo-detail"></div>
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
      <script type="text/javascript" src="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->addJs($this->theme->asset($this->config->dependencies->javascript, null, false))->
                                                                        addJs($this->theme->asset('util', null, false))->getUrl(AssetPipeline::js, 'f'); ?>"></script>
    <?php } ?>
    <script>
      OP.Util.init(jQuery, {
        eventMap: {
          'click': {
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
              'login-click':'click:login',
              'login-modal-click':'click:login-modal',
              'login-openphoto-click':'click:login-openphoto',
              'manage-password-request-click':'click:manage-password-request',
              'manage-password-reset-click':'click:manage-password-reset',
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
              'photo-view-modal-click':'click:photo-view-modal',
              'photos-load-more-click':'click:photos-load-more',
              'pin-click':'click:pin',
              'pin-clear-click':'click:pin-clear',
              'plugin-status-click':'click:plugin-status',
              'plugin-update-click':'click:plugin-update',
              'search-click':'click:search',
              'settings-click':'click:settings',
              'tags-focus':'focus:tags',
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
          }
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
                '<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->addJs('/assets/javascripts/openphoto-upload.min.js')->getUrl(AssetPipeline::js, 'g'); ?>',
              <?php } ?>
            <?php } ?>

            <?php if($this->config->site->mode === 'dev') { ?>
              '/assets/javascripts/openphoto-helper.js',
              '<?php $this->theme->asset('javascript', 'bootstrap.min.js'); ?>',

              '<?php $this->theme->asset('javascript', 'jquery.history.js'); ?>',
              '<?php $this->theme->asset('javascript', 'jquery.scrollTo.js'); ?>',
              '<?php $this->theme->asset('javascript', 'touchSwipe.js'); ?>',
              '<?php $this->theme->asset('javascript', 'browserupdate.js'); ?>',
              '<?php $this->theme->asset('javascript', 'gallery.js'); ?>',
              '<?php $this->theme->asset('javascript', 'phpjs.js'); ?>',
              '<?php $this->theme->asset('javascript', 'openphoto-theme.js'); ?>'
              /*'<?php $this->theme->asset('javascript', 'min/jquery.history.min.js'); ?>',
              '<?php $this->theme->asset('javascript', 'min/jquery.scrollTo.min.js'); ?>',
              '<?php $this->theme->asset('javascript', 'min/touchSwipe.min.js'); ?>',
              '<?php $this->theme->asset('javascript', 'min/browserupdate.min.js'); ?>',
              '<?php $this->theme->asset('javascript', 'min/gallery.min.js'); ?>',
              '<?php $this->theme->asset('javascript', 'min/phpjs.min.js'); ?>',
              '<?php $this->theme->asset('javascript', 'min/openphoto-theme.min.js'); ?>'*/
            <?php } else { ?>
            '<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->setMode(AssetPipeline::combined)->
                                                  addJs('/assets/javascripts/openphoto-helper.min.js')->
                                                  addJs($this->theme->asset('javascript', 'min/openphoto-theme-full.min.js', false))->
                                                  getUrl(AssetPipeline::js, 'y'); ?>'
            <?php } ?>
          ],
          onComplete: function(){ 
            opTheme.init.attach(); 
            opTheme.init.load('<?php $this->utility->safe($this->session->get('crumb')); ?>'); 
          }
        }
      });
    </script>
    <?php $this->plugin->invoke('renderFooter'); ?>
  </body>
</html>
