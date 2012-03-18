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
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar pull-left" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
            <span class="icon-bar"></span>
					</a>
					<a class="brand" href="/" title="The OpenPhoto Project">The OpenPhoto Project</a>
					<div class="nav-collapse">
						<ul class="nav">
              <li><a href="<?php $this->url->photosView(); ?>"><i class="icon-picture icon-large"></i> Gallery</a></li>
              <li><a href="<?php $this->url->tagsView(); ?>"><i class="icon-tag icon-large"></i> Tags</a></li>
              <?php if($this->user->isOwner()) { ?>
                <li><a href="<?php $this->url->photosUpload(); ?>"><i class="icon-upload icon-large"></i> Upload</a></li>
                <li><a href="<?php $this->url->manage(); ?>"><i class="icon-th icon-large"></i> Manage</a></li>
              <?php } ?>
						</ul>

              <ul class="nav pull-right">
                <li class="dropdown">
                  <?php if($this->user->isLoggedIn()) { ?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?php $this->utility->safe($this->user->getAvatarFromEmail(25, $this->user->getEmailAddress())); ?>" class="gravatar"/> <b class="caret"></b></a>
                  <?php } else { ?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user icon-large"></i> <b class="caret"></b></a>
                  <?php } ?>
                  <ul class="dropdown-menu">
                    <?php if($this->user->isLoggedIn()) { ?>
                      <?php if($this->user->isOwner()) { ?>
                        <li><a href="<?php $this->url->userSettings(); ?>"><i class="icon-cog icon-large"></i> Preferences</a></li>
                        <li class="divider"></li>
                      <?php } ?>
                      <li><a href="<?php $this->url->userLogout(); ?>"><i class="icon-signout icon-large"></i> Logout</a></li>
                    <?php } else { ?>
                      <li class="nav-header">Login using</li>
                      <?php if($this->plugin->isActive('FacebookConnect')) { ?>
                        <li><a href="#" class="login-click facebook" title="Signin using Facebook"><i class="icon-cog icon-large"></i> Facebook</a></li>
                      <?php } ?>
                      <li><a href="#" class="login-click browserid" title="Signin using BrowserID"><i class="icon-cog icon-large"></i> Browser ID</a></li>
                    <?php } ?>
                  </ul>
                </li>
              </ul>
						<!-- <ul class="nav pull-right">
							<li><a href="#"><i class="icon-plus icon-large"></i> Register</a></li>
							<li><a href="#"><i class="icon-signin icon-large"></i> Login</a></li>
						</ul> -->
						<form class="navbar-search pull-right" action="">
							<div class="input-append">
								<input class="search-query span2" id="appendedInput" size="16" type="text">
								<a href="" class="add-on"><i class="icon-search"></i></a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			
      <?php $this->plugin->invoke('renderBody'); ?>
      <div class="message"></div>

      <?php echo $body; ?>
      
      <div class="modal" id="modal"></div>

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
              'login-click':'click:login',
              'map-jump-click':'click:map-jump',
              'modal-close-click':'click:modal-close',
              'nav-item-click':'click:nav-item',
              'pagination-click':'click:pagination',
              'photo-delete-click':'click:photo-delete',
              'photo-edit-click':'click:photo-edit',
              'photo-update-click':'click:photo-update',
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
