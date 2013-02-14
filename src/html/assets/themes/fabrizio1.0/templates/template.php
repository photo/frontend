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
      <link href="/assets/stylesheets/upload.css" rel="stylesheet">
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

    <?php if(!$this->plugin->isActive('BetterPageTitles')) { ?>
      <title><?php $this->theme->meta('titles', $page); ?></title>
    <?php } ?>
    <?php $this->plugin->invoke('renderHead'); ?>
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
    <!--<script type="text/javascript" src="<?php $this->theme->asset($this->config->dependencies->javascript); ?>"></script>-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="<?php $this->theme->asset('util'); ?>"></script>
    <script type="text/javascript" src=""></script>

    <script type="text/javascript">
      OP.Util.init(jQuery, {
        js: {
          assets: [
            <?php if(isset($_GET['__route__']) && stristr($_GET['__route__'], 'upload')) { ?> 
              '<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->setMode(AssetPipeline::combined)->
                addJs('/assets/javascripts/plupload.js', false)->
                addJs('/assets/javascripts/plupload.html5.js', false)->
                addJs('/assets/javascripts/jquery.plupload.queue.js', false)->
                addJs('/assets/javascripts/openphoto-upload.js')->
                getUrl(AssetPipeline::js, 'a', $this->config->site->mode === 'prod'); ?>',
            <?php } ?>

              '<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->setMode(AssetPipeline::combined)->
                addJs('/assets/javascripts/openphoto-helper.js', false)->
                addJs($this->theme->asset('javascript', 'underscore-min.js', false))->
                addJs($this->theme->asset('javascript', 'backbone.js', false))->
                addJs($this->theme->asset('javascript', 'bootstrap.min.js', false))->
                addJs($this->theme->asset('javascript', 'x-editable/bootstrap-editable/js/bootstrap-editable.js', false))->
                addJs($this->theme->asset('javascript', 'phpjs.js', false))->
                addJs($this->theme->asset('javascript', 'overrides.js', false))->
                addJs($this->theme->asset('javascript', 'op/namespace.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/route/Routes.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/model/Album.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/model/Batch.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/model/Notification.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/model/Profile.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/model/Photo.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/model/Tag.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/collection/Album.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/collection/Profile.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/collection/Photo.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/collection/Tag.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/store/Albums.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/store/Profiles.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/store/Photos.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/store/Tags.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/view/Editable.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/view/BatchIndicator.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/view/AlbumCover.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/view/Notification.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/view/PhotoDetail.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/view/PhotoGallery.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/view/ProfileName.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/view/ProfilePhoto.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/view/TagSearch.js', false))->
                addJs($this->theme->asset('javascript', 'op/data/view/UserBadge.js', false))->
                addJs($this->theme->asset('javascript', 'op/Lightbox.js', false))->
                addJs($this->theme->asset('javascript', 'op/Util.js', false))->
                addJs($this->theme->asset('javascript', 'op/Strings.js', false))->
                addJs($this->theme->asset('javascript', 'op/Handlers.js', false))->
                addJs($this->theme->asset('javascript', 'op/Callbacks.js', false))->
                addJs($this->theme->asset('javascript', 'op/Format.js', false))->
                addJs($this->theme->asset('javascript', 'gallery.js', false))->
                addJs($this->theme->asset('javascript', 'fabrizio.js', false))->
                getUrl(AssetPipeline::js, 'bc', $this->config->site->mode === 'prod'); ?>'
            ], // assets
            onComplete: function() {
              OP.Util.addEventMap(TBX.handlers);
              TBX.notification.init();
              <?php if($note = $this->notification->get()) { ?>
                TBX.notification.show(<?php printf('%s, %s, %s', json_encode($this->utility->safe($note['msg'], '<a>', false)), json_encode($this->utility->safe($note['type'], false)), json_encode($this->utility->safe($note['mode'], false))); ?>);
              <?php } ?>
              TBX.init.load('<?php $this->utility->safe($this->session->get('crumb')); ?>'); 
              TBX.init.attachEvents();
              <?php $this->plugin->invoke('renderFooterJavascript'); ?>
            } // onComplete
          } // js
        });
    </script>
    <?php $this->plugin->invoke('renderFooter'); ?>
  </body>
</html>
