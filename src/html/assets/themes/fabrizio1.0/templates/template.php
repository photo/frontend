<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>TroveBox </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    
    <!-- link href="../../assets/css/bootstrap.css" rel="stylesheet" -->
    <!--<link href="../../theme/style/dev.css.php" rel="stylesheet">-->
    <link href="/assets/themes/fabrizio1.0/javascripts/x-editable/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet" />
    <?php if($this->config->site->mode === 'dev') { ?>
      <link href="/assets/themes/fabrizio1.0/stylesheets/lessc?f=less/index.less" rel="stylesheet">
      <?php if(isset($_GET['__route__']) && stristr($_GET['__route__'], 'upload')) { ?> 
        <link href="/assets/stylesheets/upload.css" rel="stylesheet">
      <?php } ?>
    <?php } else { ?>
      <link rel="stylesheet" href="<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->
                                                                  addCss("/assets/themes/fabrizio1.0/stylesheets/lessc?f=less/index.less")->
                                                                  getUrl(AssetPipeline::css, 'a'); ?>">
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
    <div class="footer">
      <div class="container">
        <hr>
        footer
      </div>
    </div>
    <?php $this->theme->display('partials/underscore.php'); ?>
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!--<script type="text/javascript" src="<?php $this->theme->asset($this->config->dependencies->javascript); ?>"></script>-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="<?php $this->theme->asset('util'); ?>"></script>
    <script type="text/javascript" src="/assets/javascripts/openphoto-helper.js"></script>

    <script src="<?php $this->theme->asset('javascript', 'underscore-min.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'backbone.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'bootstrap.min.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'x-editable/bootstrap-editable/js/bootstrap-editable.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'phpjs.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'overrides.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/namespace.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/model/Album.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/model/Batch.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/model/Notification.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/model/Profile.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/model/Photo.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/collection/Album.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/collection/Profile.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/collection/Photo.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/store/Albums.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/store/Profiles.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/store/Photos.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/view/Editable.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/view/BatchIndicator.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/view/AlbumCover.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/view/Notification.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/view/PhotoDetail.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/view/PhotoGallery.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/view/ProfileName.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/view/ProfilePhoto.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/data/view/UserBadge.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'op/Lightbox.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'gallery.js'); ?>"></script>
    <script src="<?php $this->theme->asset('javascript', 'fabrizio.js'); ?>"></script>
    <?php if(isset($_GET['__route__']) && stristr($_GET['__route__'], 'upload')) { ?> 
      <?php if(true || $this->config->site->mode === 'dev') { ?>
        <script src="/assets/javascripts/plupload.js"></script>
        <script src="/assets/javascripts/plupload.html5.js"></script>
        <script src="/assets/javascripts/jquery.plupload.queue.js"></script>
        <script src="/assets/javascripts/openphoto-upload.js"></script>
      <?php } else { ?>
        '<?php $this->utility->safe($this->config->site->cdnPrefix);?><?php echo getAssetPipeline(true)->addJs('/assets/javascripts/openphoto-upload.min.js')->getUrl(AssetPipeline::js, 'q'); ?>',
      <?php } ?>
    <?php } ?>

    <script type="text/javascript">
      OP.Util.init(jQuery, {
        eventMap: TBX.handlers,
        js: {
          assets: [
            '<?php $this->theme->asset('javascript', 'fabrizio.js'); ?>'
          ],
          onComplete: function() {
            TBX.notification.init();
            <?php if($note = $this->notification->get()) { ?>
              TBX.notification.show(<?php printf('%s, %s, %s', json_encode($this->utility->safe($note['msg'], '<a>', false)), json_encode($this->utility->safe($note['type'], false)), json_encode($this->utility->safe($note['mode'], false))); ?>);
            <?php } ?>
            TBX.init.load('<?php $this->utility->safe($this->session->get('crumb')); ?>'); 
            TBX.init.attachEvents();
          }
        }
      });
    </script>
    <!--
      OP.Util.init(jQuery, {
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
              '<?php $this->theme->asset('javascript', 'underscore-min.js'); ?>',
              '<?php $this->theme->asset('javascript', 'backbone.js'); ?>',
              '<?php $this->theme->asset('javascript', 'bootstrap.min.js'); ?>',
              '<?php $this->theme->asset('javascript', 'x-editable/bootstrap-editable/js/bootstrap-editable.js'); ?>',
              '<?php $this->theme->asset('javascript', 'phpjs.js'); ?>',
              '<?php $this->theme->asset('javascript', 'data.js'); ?>',
              '<?php $this->theme->asset('javascript', 'gallery.js'); ?>',
              '<?php $this->theme->asset('javascript', 'fabrizio.js'); ?>'
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
          ],*/
          onComplete: function(){ 
            TBX.init.load('<?php $this->utility->safe($this->session->get('crumb')); ?>'); 
          }
        }
      });
      jQuery(function($){
        $('.example-icon-list li').hover(function(){
          $(this).find('i').addClass('tb-icon-highlight');
        }, function(){
          $(this).find('i').removeClass('tb-icon-highlight');
        });
      });*/
    -->
    <?php $this->plugin->invoke('renderFooter'); ?>
  </body>
</html>
