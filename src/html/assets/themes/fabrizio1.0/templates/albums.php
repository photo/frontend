<?php if(!empty($albums)) { ?>
  <?php if(count($albums) >= 8 || (isset($_GET['page']) && $_GET['page'] > 1)) { ?>
    <div class="pagination">
      <ul role="navigation">
        <?php /* TODO this is really crude */ ?>
        <?php if(isset($_GET['page']) && $_GET['page'] > 1) { ?>
          <li><a href="<?php $this->utility->safe(preg_replace('/page=\d+/', 'page='.intval($_GET['page']-1), $_SERVER['REQUEST_URI'])); ?>">&larr; Prev</a></li>
        <?php } ?>
        <?php if(count($albums) >= 8) { ?>
          <?php if(stristr($_SERVER['REQUEST_URI'], 'page=')) { ?>
            <li><a href="<?php $this->utility->safe(preg_replace('/page=\d+/', 'page='.intval($_GET['page']+1), $_SERVER['REQUEST_URI'])); ?>">Next &rarr;</a></li>
          <?php } else { ?>
            <li><a href="<?php $this->utility->safe($_SERVER['REQUEST_URI']); ?>?page=2">Next &rarr;</a></li>
          <?php } ?>
        <?php } ?>
      </ul>
    </div>
  <?php } ?>
  
  <div class="row album-row hero-unit empty">
    <div class="album-list span12">
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
