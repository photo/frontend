<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    
    <link href="//fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic&.css" rel="stylesheet">
    <?php if($this->config->site->mode === 'dev') { ?>
      <link href="/assets/themes/fabrizio1.0/stylesheets/lessc?f=less/index.less" rel="stylesheet">
    <?php } else { ?>
      <link href="<?php $this->utility->safe($this->config->site->cdnPrefix); ?>/assets/versioned/<?php $this->utility->safe($this->config->site->mediaVersion); ?>/themes/fabrizio1.0/stylesheets/lessc?f=less/index.less" rel="stylesheet">
    <?php } ?>
    <?php if(isset($_GET['__route__']) && stristr($_GET['__route__'], 'upload')) { ?> 
    <link href="<?php $this->utility->safe($this->config->site->cdnPrefix); ?>/assets/versioned/<?php $this->utility->safe($this->config->site->mediaVersion); ?>/stylesheets/upload.css" rel="stylesheet">
    <?php } ?>

    <link rel="shortcut icon" href="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php $this->theme->asset('image', 'favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php $this->theme->asset('image', 'apple-touch-icon.png'); ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php $this->theme->asset('image', 'apple-touch-icon-72x72.png'); ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php $this->theme->asset('image', 'apple-touch-icon-114x114.png'); ?>">
      
    <!-- <link href="../../assets/css/bootstrap-responsive.css" rel="stylesheet"> -->

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <?php $this->theme->display('partials/titles.php'); ?>
  </head>

  <body class="trovebox <?php $this->utility->safe($page); ?>">
    <div class="navbar navbar-inverse navbar-fixed-top trovebox-banner">
      <?php $this->theme->display('partials/header.php'); ?>
    </div>

    <div class="container">
      <?php $this->plugin->invoke('renderBody'); ?>
      <?php echo $body; ?>
    </div> <!-- /container -->
    <?php $this->theme->display('partials/footer.php'); ?>

    <?php $this->theme->display('partials/underscore.php'); ?>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->setMode(AssetPipeline::combined)->
      addJs('/assets/javascripts/openphoto-util.js', false)->
      addJs('/assets/javascripts/openphoto-helper.js', false)->
      getUrl(AssetPipeline::js, $this->config->site->mediaVersion, $this->config->site->mode === 'prod'); ?>">
    </script>

    <script type="text/javascript">
      OP.Util.init(jQuery, {
        js: {
          assets: [
            <?php if(isset($_GET['__route__']) && stristr($_GET['__route__'], 'upload')) { ?> 
              <?php if($this->config->site->mode === 'prod') { ?>
                '<?php printf('%s%s',
                  $this->utility->safe($this->config->site->cdnPrefix),
                  getAssetPipeline(true)->setMode(AssetPipeline::combined)->
                    addJs(sprintf('/assets/javascripts/releases/%s/upload.js', $this->config->defaults->currentCodeVersion), false)->
                    getUrl(AssetPipeline::js, $this->config->site->mediaVersion, true/*$this->config->site->mode === 'prod'*/)
                ); ?>',
              <?php } else { ?>
                '<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->setMode(AssetPipeline::combined)->
                  addJs('/assets/javascripts/plupload.js', false)->
                  addJs('/assets/javascripts/plupload.html5.js', false)->
                  addJs('/assets/javascripts/jquery.plupload.queue.js', false)->
                  addJs('/assets/javascripts/openphoto-upload.js')->
                  addJs($this->theme->asset('javascript', 'dropzone.js', false))->
                  getUrl(AssetPipeline::js, $this->config->site->mediaVersion, false/*$this->config->site->mode === 'prod'*/); ?>',
              <?php } ?>
            <?php } ?>
            <?php if($this->config->site->mode === 'prod') { ?>
              '<?php printf('%s%s',
                $this->utility->safe($this->config->site->cdnPrefix),
                getAssetPipeline(true)->setMode(AssetPipeline::combined)->
                  addJs(sprintf('/assets/javascripts/releases/%s/tbx.js', $this->config->defaults->currentCodeVersion), false)->
                  getUrl(AssetPipeline::js, $this->config->site->mediaVersion, true/*$this->config->site->mode === 'prod'*/)
              ); ?>',
            <?php } else { ?>
              '<?php $this->utility->getJSAssetsURL(); ?>'
            <?php } ?>
            ], // assets
            onComplete: function() {
              OP.Util.addEventMap(TBX.handlers);
              TBX.notification.init();
              <?php if($note = $this->notification->get()) { ?>
                TBX.notification.show(<?php printf('%s, %s, %s', json_encode($this->utility->safe($note['msg'], '<a>', false)), json_encode($this->utility->safe($note['type'], false)), json_encode($this->utility->safe($note['mode'], false))); ?>);
              <?php } ?>
              TBX.util.enableBetaFeatures(<?php echo json_encode((bool)$this->config->site->enableBetaFeatures); ?>);
              TBX.init.load('<?php $this->utility->safe($this->session->get('crumb')); ?>'); 
              <?php $this->plugin->invoke('renderFooterJavascript'); ?>
              TBX.init.run(); 
              TBX.init.attachEvents();
            } // onComplete
          } // js
        });
    </script>
    <?php $this->plugin->invoke('renderFooter'); ?>
  </body>
</html>
<?php if($this->user->isAdmin()) { ?><!-- current code version is <?php $this->utility->safe($this->config->defaults->currentCodeVersion); ?> --><?php } ?>
