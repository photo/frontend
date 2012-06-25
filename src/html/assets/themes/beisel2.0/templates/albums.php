<?php if(!empty($albums)) { ?>
  <div class="row album-row hero-unit empty">
    <div class="album-list span12">
      <h3>Albums <small class="show-all">(<a href="#" class="album-show-all-click">show all</a>)</small></h3>
      <ul class="thumbnails">
        <?php foreach($albums as $alb) { ?>
          <li>
            <a href="<?php $this->url->photosView(sprintf('album-%s', $alb['id'])); ?>">
              <?php if(empty($alb['cover'])) { ?>
                <i class="icon-picture icon-large"></i>
              <?php } else { ?>
                <div style="background-image:url('<?php $this->utility->safe($alb['cover']['path200x200xCR']); ?>');"></div>
              <?php } ?>
            </a>
            <h5><?php $this->utility->safe($alb['name']); ?></h5>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
<?php } else { ?>
  <?php $this->theme->display('partials/no-content.php', array('type' => 'albums')); ?>
<?php } ?>
