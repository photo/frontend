<div data-role="page" data-add-back-btn="true" id="Gallery1" class="gallery-page" data-theme="c">
  <div data-role="header" class="photoheader" data-theme="c">
    <a href="/" data-icon="home" class="ui-btn-right" rel="external">Home</a>
  </div>
	<div data-role="content">
    <?php if(!empty($photos)) { ?>
      <ul class="gallery">
        <?php foreach($photos as $photo) { ?>
          <li><a href="<?php Url::photoUrl($photo, getConfig()->get('photoSizes')->detail); ?>" rel="external"><img src="<?php Url::photoUrl($photo, getConfig()->get('photoSizes')->thumbnail); ?>" alt="Image 001" /></a></li>
        <?php } ?>
      </ul>
      <?php getTheme()->display('partials/pagination.php', array_merge(array('labelPosition' => 'bottom'), $pages)); ?>
    <?php } else { ?>
      <h2>Sorry, no photos to see.</h2>
    <?php } ?>
	</div>

	<div data-role="footer" data-theme="c">
    <h4>The OpenPhoto Project &#169; <?php echo date('Y'); ?></h4>
	</div>
</div>

