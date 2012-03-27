<?php $thumbnailSize = isset($_GET['size']) ? $_GET['size'] : '960x180'; ?>
<div class="subnav js-hide">
  <ul class="nav nav-pills">
    <!--<li><a href="#"><i class="icon-th icon-large"></i> Small</a></li>
    <li class="active"><a href="#"><i class="icon-th-large icon-large"></i> Big</a></li>
    <li><a href="#"><i class="icon-th-list icon-large"></i> List</a></li>-->
    <!-- <li><a href="#"><i class="icon-asterisk icon-large"></i> Justified</a></li> -->
    <?php if($this->user->isOwner()) { ?>
      <li class="last"><a href="/manage"><i class="icon-edit icon-large"></i> Manage photos</a></li>
    <?php } ?>
  </ul>
</div>

<div class="js-hide">
  <?php $this->theme->display('partials/pagination.php', $pages); ?>
</div>

<div class="row hero-unit empty gallery">
  <?php if($photos[0]['totalRows'] > 0) { ?>
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
  <?php } ?>
  <div class="photo-grid-justify"><p class="page-hr">Page <?php $this->utility->safe($pages['currentPage']); ?></p></div>
  <br clear="all">
</div>
