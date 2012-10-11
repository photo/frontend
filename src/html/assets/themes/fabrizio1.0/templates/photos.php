<?php if($photos[0]['totalRows'] > 0) { ?>
  <?php $minDate = min($photos[0]['dateTaken'], $photos[count($photos)-1]['dateTaken']); ?>
  <?php $maxDate = max($photos[count($photos)-1]['dateTaken'], $photos[count($photos)-1]['dateTaken']); ?>
  <div class="infobar subnav subnav-fixed">
    <ul class="nav nav-pills">
      <li class="plain"><a>Showing photos between <i class="icon-calendar icon-large"></i> <span class="startdate date" data-time="<?php $this->utility->safe($minDate); ?>"><?php $this->utility->safe(date('l F jS, Y', $minDate)); ?></span> and <i class="icon-calendar icon-large"></i> <span class="enddate date" data-time="<?php $this->utility->safe($maxDate); ?>"><?php $this->utility->safe(date('l F jS, Y', $maxDate)); ?></span></a></li>
    </ul>
  </div>

  <div class="row hero-unit empty gallery">
    <?php if(!empty($album)) { ?>
      <div class="header">Photos from <i class="icon-th icon-large"></i> <?php $this->utility->safe($album['name']); ?></div>
    <?php } elseif(!empty($tags)) { ?>
      <div class="header">
        <i class="icon-tags icon-large"></i>
        Photos tagged with
        <?php foreach($tags as $cnt => $tag) { ?><?php $this->utility->safe($tag); ?><?php if(count($tags) > 1 && $cnt < (count($tags)-1)) { ?><?php if($cnt < (count($tags)-2)) { ?>, <?php } else { ?> and <?php } ?><?php } ?>
        <?php } ?>
      </div>
    <?php } ?>
    <div class="photo-grid-justify"></div>
    <br clear="all">
    <?php if($photos[0]['totalPages'] > 1) { ?>
      <div class="load-more">
        <button type="button" class="span2 btn btn-primary photos-load-more-click"><i class="icon-plus icon-large"></i> Load more</button>
      </div>
    <?php } ?>
  </div>
  <script> var initData = <?php echo json_encode($photos); ?>; var filterOpts = <?php echo json_encode($options); ?>;</script>
<?php } else { ?>
  <?php $this->theme->display('partials/no-content.php', array('type' => 'upload')); ?>
<?php } ?>
