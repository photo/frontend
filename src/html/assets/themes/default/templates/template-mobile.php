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
    <link rel="shortcut icon" href="<?php $this->theme->asset('image', 'favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php $this->theme->asset('image', 'apple-touch-icon.png'); ?>">
    <?php if($this->config->site->mode === 'dev') { ?>
      <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'jquery.mobile.css'); ?>">
      <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'photoswipe.css'); ?>">
      <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'pagination.css'); ?>">
      <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'mobile.css'); ?>">
    <?php } else { ?>
      <link rel="stylesheet" href="<?php echo getAssetPipeline(true)->addCss($this->theme->asset('stylesheet', 'jquery.mobile.css', false))->
                                        addCss($this->theme->asset('stylesheet', 'photoswipe.css', false))->
                                        addCss($this->theme->asset('stylesheet', 'pagination.css', false))->
                                        addCss($this->theme->asset('stylesheet', 'mobile.css', false))->
                                        getUrl(AssetPipeline::css, 'a'); ?>">
    <?php } ?>


    <?php $this->plugin->invoke('renderHead'); ?>
</head>
<body class="<?php echo $page; ?>">
    <?php echo $body; ?>
    <?php if($this->config->site->mode === 'dev') { ?>
      <script type="text/javascript" src="<?php $this->theme->asset($this->config->dependencies->javascript); ?>"></script>
      <script type="text/javascript" src="<?php $this->theme->asset('util'); ?>"></script>
    <?php } else { ?>
      <script type="text/javascript" src="<?php echo getAssetPipeline(true)->setMode(AssetPipeline::combined)->
                                      addJs($this->theme->asset('util', null, false))->
                                      addJs($this->theme->asset($this->config->dependencies->javascript, null, false))->
                                      getUrl(AssetPipeline::js, 'e'); ?>"></script>
    <?php } ?>
    <script>
        OP.Util.init(jQuery, {
            eventMap: {
              click: {
                'login-click':'click:login'
              }
            },
            js: {
                assets: [
          <?php if($this->config->site->mode === 'dev') { ?>
                    '<?php $this->theme->asset('javascript', 'jquery.mobile.js'); ?>',
                    '<?php $this->theme->asset('javascript', 'openphoto-theme-mobile.js'); ?>'
          <?php } else { ?>
                    '<?php echo getAssetPipeline(true)->setMode(AssetPipeline::combined)->
                                              addJs($this->theme->asset('javascript', 'jquery.mobile.js', false))->
                                              addJs($this->theme->asset('javascript', 'openphoto-theme-mobile.js', false))->
                                              getUrl(AssetPipeline::js, 'b'); ?>'
          <?php } ?>
                    ],
                onComplete: function(){ opTheme.init.attach(); }
            },
        });
    </script>
    <?php $this->plugin->invoke('renderFooter'); ?>
</body>
</html>
