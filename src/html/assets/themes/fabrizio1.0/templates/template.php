<?php $this->theme->setTheme(); // force this as the default theme ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
		<meta name="language" content="english,en" />

		<meta name="distribution" content="global" />
		<meta name="revisit-after" content="7 days" />
    <meta name="robots" content="<?php if($this->config->site->hideFromSearchEngines == 1) { ?>noindex, nofollow<?php } else { ?>index,follow<?php } ?>">
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
      <link href="/assets/stylesheets/font-awesome-2.css" rel="stylesheet">
      <link href="<?php $this->theme->asset('stylesheet', 'chosen.css'); ?>" rel="stylesheet">
      <link href="<?php $this->theme->asset('stylesheet', 'opme.css'); ?>" rel="stylesheet">
      <link href="/assets/stylesheets/upload.css" rel="stylesheet">
      <!--[if IE 7]>
        <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'font-awesome-2-ie7.css'); ?>">
      <![endif]-->
    <?php } else { ?>
      <link rel="stylesheet" href="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->addCss($this->theme->asset('stylesheet', 'bootstrap.min.css', false))->
                                                                  addCss("/assets/stylesheets/font-awesome-2.css")->
                                                                  addCss("/assets/stylesheets/upload.css")->
                                                                  addCss($this->theme->asset('stylesheet', 'chosen.css', false))->
                                                                  addCss($this->theme->asset('stylesheet', 'opme.css', false))->
                                                                  getUrl(AssetPipeline::css, 'ao'); ?>">
      <!--[if IE 7]>
        <link rel="stylesheet" href="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->addCss($this->theme->asset('stylesheet', 'font-awesome-2-ie7.css', false))->
                                                                  getUrl(AssetPipeline::css, 'a'); ?>">
      <![endif]-->
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
                                                                        addJs($this->theme->asset('util', null, false))->getUrl(AssetPipeline::js, 'g'); ?>"></script>
    <?php } ?>
    <script>
      OP.Util.init(jQuery, {
        eventMap: {
          'click': {
              'action-delete-click':'click:action-delete',
              'action-jump-click':'click:action-jump',
              'action-post-click':'click:action-post',
              'album-delete-click':'click:album-delete',
              'album-form-click':'click:album-form',
              'batch-modal-click':'click:batch-modal',
              'credential-view-click':'click:credential-view',
              'credential-delete-click':'click:credential-delete',
              'group-delete-click':'click:group-delete',
              'group-email-add-click':'click:group-email-add',
              'group-email-remove-click':'click:group-email-remove',
              'group-form-click':'click:group-form',
              'login-click':'click:login',
              'login-modal-click':'click:login-modal',
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
              'pin-select-all-click':'click:pin-select-all',
              'plugin-view-click':'click:plugin-view',
              'plugin-status-toggle-click':'click:plugin-status-toggle',
              'popup-click':'click:popup',
              'settings-click':'click:settings',
              'share-facebook-click':'click:share-facebook',
              'tags-focus':'focus:tags',
              'upload-start-click':'click:upload-start',
              'webhook-delete-click':'click:webhook-delete'
          },
          'submit': {
              'album-post-submit':'submit:album-post',
              'features-post-submit':'submit:features-post',
              'group-post-submit':'submit:group-post',
              'login-openphoto-submit':'submit:login-openphoto',
              'photo-update-submit':'submit:photo-update',
              'photo-upload-submit':'submit:photo-upload',
              'plugin-update-submit':'submit:plugin-update',
              'search-submit':'submit:search'
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
                '<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->addJs('/assets/javascripts/openphoto-upload.min.js')->getUrl(AssetPipeline::js, 'q'); ?>',
              <?php } ?>
            <?php } ?>

            <?php if($this->config->site->mode === 'dev') { ?>
              '/assets/javascripts/openphoto-helper.js',
              '<?php $this->theme->asset('javascript', 'bootstrap.min.js'); ?>',
              '<?php $this->theme->asset('javascript', 'chosen.jquery.js'); ?>',
              '<?php $this->theme->asset('javascript', 'jquery.history.js'); ?>',
              '<?php $this->theme->asset('javascript', 'jquery.scrollTo.js'); ?>',
              '<?php $this->theme->asset('javascript', 'touchSwipe.js'); ?>',
              '<?php $this->theme->asset('javascript', 'gallery.js'); ?>',
              '<?php $this->theme->asset('javascript', 'phpjs.js'); ?>',
              '<?php $this->theme->asset('javascript', 'openphoto-theme.js'); ?>'
            <?php } else { ?>
            '<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->setMode(AssetPipeline::combined)->
                                                  addJs('/assets/javascripts/openphoto-helper.min.js')->
                                                  addJs($this->theme->asset('javascript', 'min/openphoto-theme-full.min.js', false))->
                                                  // debugging
                                                  /*addJs($this->theme->asset('javascript', 'min/01-bootstrap.min.js', false))->
                                                  addJs($this->theme->asset('javascript', 'min/01a-chosen.jquery.min.js', false))->
                                                  addJs($this->theme->asset('javascript', 'min/06-jquery.history.min.js', false))->
                                                  addJs($this->theme->asset('javascript', 'min/07-jquery.scrollTo.min.js', false))->
                                                  addJs($this->theme->asset('javascript', 'min/10-touchSwipe.min.js', false))->
                                                  addJs($this->theme->asset('javascript', 'min/05-gallery.min.js', false))->
                                                  addJs($this->theme->asset('javascript', 'min/09-phpjs.min.js', false))->
                                                  addJs($this->theme->asset('javascript', 'openphoto-theme.js', false))->*/
                                                  /*addJs($this->theme->asset('javascript', 'bootstrap.min.js', false))->
                                                  addJs($this->theme->asset('javascript', 'chosen.jquery.js', false))->
                                                  addJs($this->theme->asset('javascript', 'jquery.history.js', false))->
                                                  addJs($this->theme->asset('javascript', 'jquery.scrollTo.js', false))->
                                                  addJs($this->theme->asset('javascript', 'touchSwipe.js', false))->
                                                  addJs($this->theme->asset('javascript', 'browserupdate.js', false))->
                                                  addJs($this->theme->asset('javascript', 'gallery.js', false))->
                                                  addJs($this->theme->asset('javascript', 'phpjs.js', false))->
                                                  addJs($this->theme->asset('javascript', 'openphoto-theme.js', false))->*/
                                                  getUrl(AssetPipeline::js, 'aw'); ?>'
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
