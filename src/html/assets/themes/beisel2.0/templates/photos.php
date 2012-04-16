<?php $thumbnailSize = isset($_GET['size']) ? $_GET['size'] : '960x180'; ?>
<?php $minDate = min($photos[0]['dateTaken'], $photos[count($photos)-1]['dateTaken']); ?>
<?php $maxDate = max($photos[count($photos)-1]['dateTaken'], $photos[count($photos)-1]['dateTaken']); ?>
<?php if($photos[0]['totalRows'] > 0) { ?>
  <div class="infobar subnav subnav-fixed">
    <ul class="nav nav-pills">
      <li class="plain"><a>Showing photos between <i class="icon-calendar icon-large"></i> <span class="startdate date" data-time="<?php $this->utility->safe($minDate); ?>"><?php $this->utility->safe(date('l F jS, Y', $minDate)); ?></span> and <i class="icon-calendar icon-large"></i> <span class="enddate date" data-time="<?php $this->utility->safe($maxDate); ?>"><?php $this->utility->safe(date('l F jS, Y', $maxDate)); ?></span></a></li>
    </ul>
  </div>

  <?php if(count($pages) > 1) { ?>
    <div class="js-hide">
      <?php $this->theme->display('partials/pagination.php', $pages); ?>
    </div>
  <?php } ?>
<?php } ?>

<?php if($photos[0]['totalRows'] > 0) { ?>
  <div class="row hero-unit empty gallery">
    <ul class="photo-grid js-hide">
      <?php foreach($photos as $photo) { ?>
        <li>
          <div class="shell">
            <a href="<?php $this->url->photoView($photo['id'], $options); ?>">
              <img src="<?php $this->url->photoUrl($photo, $thumbnailSize); ?>" alt="<?php $this->utility->safe($photo['title']); ?>" class="thumb" />
            </a>
            <span class="meta">
              <a href="" class="invert"><i class="icon-heart"></i>4x </a>
              <a href="" class="invert"><i class="icon-tag"></i><?php echo count($photo['tags']); ?>x </a>
            </span>
          </div>
        </li>
      <?php } ?>
    </ul>
    <div class="photo-grid-justify"></div>
    <br clear="all">
    <?php if($photos[0]['totalPages'] > 1) { ?>
      <div class="load-more">
        <button type="button" class="span2 btn btn-primary photos-load-more-click"><i class="icon-plus icon-large"></i> Load more</button>
      </div>
    <?php } ?>
  </div>
<?php } else { ?>
  <?php $this->theme->display('partials/no-content.php', array('type' => 'upload')); ?>
<?php } ?>
