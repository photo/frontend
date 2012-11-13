<div data-role="page" data-add-back-btn="true" data-theme="c">
  <div data-role="header" class="photoheader" data-theme="c">
    <a href="/" data-icon="home" class="ui-btn-right" rel="external">Home</a>
  </div>
	<div data-role="content">
    <div class="photo-details">
      <div class="image-pagination">
        <?php if(!empty($photo['previous'])) { ?>
          <div class="previous">
            <a href="<?php $this->url->photoView($photo['previous'][0]['id'], $options); ?>" style="background:url(<?php $this->url->photoUrl($photo['previous'][0], $this->config->photoSizes->nextPrevious); ?>) top left no-repeat;" data-direction="reverse"><span class="audible">Go to previous photo</span></a>
          </div>
        <?php } else { ?>
          <div class="empty"></div>
        <?php } ?>
        <?php if(!empty($photo['next'])) { ?>
          <div class="next">
            <a href="<?php $this->url->photoView($photo['next'][0]['id'], $options); ?>" style="background:url(<?php $this->url->photoUrl($photo['next'][0], $this->config->photoSizes->nextPrevious); ?>) top left no-repeat"><span class="audible">Go to next photo</span></a>
          </div>
        <?php } else { ?>
          <div class="empty"></div>
        <?php } ?>
      </div>
      <div class="photo-column">
        <img class="photo" src="<?php $this->url->photoUrl($photo, $this->config->photoSizes->detail); ?>" alt="<?php $this->utility->safe($photo['title']); ?>">
        <h1><?php $this->utility->safe($photo['title']); ?></h1>
        <p class="description"><?php $this->utility->safe($photo['description']); ?></p>
      </div>
      <ul class="meta">
        <li class="date"><?php $this->utility->dateLong($photo['dateTaken']); ?></li>
        <!--<li class="heart"><?php echo count($photo['actions']); ?> favorites &amp; comments - <a href="#comments" class="action-jump-click">see all</a></li>-->
        <li class="tags"><?php $this->url->tagsAsLinks($photo['tags']); ?></li>
        <?php if(isset($photo['license'])) { ?>
          <li class="license"><?php $this->utility->licenseLong($photo['license']); ?></li>
        <?php } ?>
        <?php if(!empty($photo['latitude']) && !empty($photo['latitude'])) { ?>
          <li class="location">
            <a href="<?php $this->utility->mapLinkUrl($photo['latitude'], $photo['longitude'], 5); ?>"><?php $this->utility->safe($photo['latitude']); ?>, <?php $this->utility->safe($photo['longitude']); ?></a>
            <img src="<?php $this->utility->staticMapUrl($photo['latitude'], $photo['longitude'], 5, '225x150'); ?>" class="map">
          </li>
        <?php } ?>
        <li class="exif">
          <ul>
            <?php foreach(array('exifCameraMake' => 'Camera make: %s',
                                        'exifCameraModel' => 'Camera model: %s',
                                        'exifFNumber' => 'Av: f/%1.1F',
                                        'exifExposureTime' => 'Tv: %s',
                                        'exifISOSpeed' => 'ISO: %d',
                                        'exifFocalLength' => 'Focal Length: %1.0fmm') as $key => $value) { ?>
              <?php if(!empty($photo[$key])) { ?>
                <li><?php printf($value, $this->utility->safe($photo[$key], false)); ?></li>
              <?php } ?>
            <?php } ?>
          </ul>
        </li>
      </ul>
    </div>
	</div>

	<div data-role="footer" data-theme="c">
    <h4>The OpenPhoto Project &#169; <?php echo date('Y'); ?></h4>
	</div>
</div>
