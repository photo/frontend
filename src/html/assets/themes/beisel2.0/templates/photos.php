<?php if($photos[0]['totalRows'] > 0) { ?>
  <?php $minDate = min($photos[0]['dateTaken'], $photos[count($photos)-1]['dateTaken']); ?>
  <?php $maxDate = max($photos[count($photos)-1]['dateTaken'], $photos[count($photos)-1]['dateTaken']); ?>
  <div class="infobar subnav subnav-fixed">
    <ul class="nav nav-pills">
      <li class="plain"><a>Showing photos between <i class="icon-calendar icon-large"></i> <span class="startdate date" data-time="<?php $this->utility->safe($minDate); ?>"><?php $this->utility->safe(date('l F jS, Y', $minDate)); ?></span> and <i class="icon-calendar icon-large"></i> <span class="enddate date" data-time="<?php $this->utility->safe($maxDate); ?>"><?php $this->utility->safe(date('l F jS, Y', $maxDate)); ?></span></a></li>
    </ul>
  </div>
  
  <div class="album-row">
    <?php if(!empty($albums)) { ?>
      <div class="album-list">
        <h3>Albums <small class="show-all">(<a href="#" class="album-show-all-click">show all</a>)</small></h3>
        <ul class="thumbnails">
          <?php foreach($albums as $alb) { ?>
            <?php if($alb['count'] > 0) { ?>
              <li>
                <a href="<?php $this->url->photosView(sprintf('album-%s', $alb['id'])); ?>"><img src="<?php $this->theme->asset('image', 'album-placeholder.png'); ?>"></a>
                <h5><?php $this->utility->safe($alb['name']); ?></h5>
              </li>
            <?php } ?>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>
  </div>

  <div class="row hero-unit empty gallery">
    <?php if(!empty($album)) { ?>
      <p class="album-name">
        <h4>Photos from <?php $this->utility->safe($album['name']); ?></h4>
      </p>
    <?php } ?>
    <div class="photo-grid-justify"></div>
    <br clear="all">
    <?php if($photos[0]['totalPages'] > 1) { ?>
      <div class="load-more">
        <button type="button" class="span2 btn btn-primary photos-load-more-click"><i class="icon-plus icon-large"></i> Load more</button>
      </div>
    <?php } ?>
  </div>
  <script> var initData = <?php echo json_encode($photos); ?>;</script>
<?php } else { ?>
  <?php $this->theme->display('partials/no-content.php', array('type' => 'upload')); ?>
<?php } ?>
