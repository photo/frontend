<div class="manage home">

  <?php $this->template->display(dirname(__FILE__).'/manage-navigation.php', array('page' => $page)); ?>
  <?php $this->theme->display('partials/pagination.php', $pages); ?>

  <ul class="thumbnails">
    <?php foreach($photos as $photo) { ?>
      <li class="span2">
        <img src="<?php $this->utility->safe($photo['path160x160xCR']); ?>" alt="<?php $this->utility->safe($photo['title']); ?>" class="thumbnail">
        <i class="icon-ok icon-large photo-<?php $this->utility->safe($photo['id']); ?> pin-click pin reveal" data-id="<?php $this->utility->safe($photo['id']); ?>"></i>
        <i class="icon-edit icon-large photo-<?php $this->utility->safe($photo['id']); ?> photo-edit-click edit reveal" data-id="<?php $this->utility->safe($photo['id']); ?>"></i>
      </li>
    <?php } ?>
  </ul>
</div>
