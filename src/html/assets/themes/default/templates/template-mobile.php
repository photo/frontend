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
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'jquery.mobile.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'photoswipe.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'pagination.css'); ?>">
    <link rel="stylesheet" href="<?php $this->theme->asset('stylesheet', 'mobile.css'); ?>">
    <?php $this->plugin->invoke('onHead', array('page' => $page)); ?>
</head>
<body class="<?php echo $page; ?>">
    <?php $this->plugin->invoke('onBodyBegin', array('page' => $page)); ?>
    <?php echo $body; ?>
    <script type="text/javascript" src="<?php $this->theme->asset($this->config->dependencies->javascript); ?>"></script>
    <script type="text/javascript" src="<?php $this->theme->asset('util'); ?>"></script>
    <script>
        OP.Util.init(jQuery, {
            js: {
                assets: [
                    '<?php $this->theme->asset('javascript', 'jquery.mobile.js'); ?>',
                    '<?php $this->theme->asset('javascript', 'openphoto-theme-mobile.js'); ?>'
                    ]
            }
        });
    </script>
    <?php $this->plugin->invoke('onBodyEnd', array('page' => $page)); ?>
</body>
</html>
