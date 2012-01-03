<div data-role="page" data-add-back-btn="true" id="Gallery1" class="gallery-page" data-theme="c">
  <div data-role="header" class="photoheader" data-theme="c">
    <a href="/" data-icon="home" class="ui-btn-right" rel="external">Home</a>
  </div>
	<div data-role="content">
    <?php if(!empty($photos)) { ?>
      <ul class="gallery">
        <?php foreach($photos as $photo) { ?>
          <li><a href="<?php $this->url->photoView($photo['id'], $options); ?>" rel="external"><img src="<?php $this->url->photoUrl($photo, $this->config->photoSizes->thumbnail); ?>" alt="Image 001" /></a></li>
        <?php } ?>
      </ul>
      <?php $this->theme->display('partials/pagination.php', array_merge(array('labelPosition' => 'bottom'), $pages)); ?>
    <?php } else { ?>
      <h2>Sorry, no photos to see.</h2>
    <?php } ?>
	</div>

	<div data-role="footer" data-theme="c">
    <h4>The OpenPhoto Project &#169; <?php echo date('Y'); ?></h4>
	</div>
</div>

