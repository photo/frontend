<div id="photos">
  <?php if($photos[0]['totalRows'] > 0) { ?>
    <?php getTemplate()->display('partial/pagination.php', $pagination); ?>
    <ul class="grid">
      <?php foreach($photos as $photo) { ?>
        <li class="photo-container-<?php echo $photo['id']; ?>">
          <a href="/photo/<?php echo $photo['id']; ?>"><img src="<?php echo $photo['path200x200xCR']; ?>"></a>
          <ul class="options">
            <li><a href="#"><img src="/assets/img/default/icon-add.png"></a></li>
            <li><a href="#"><img src="/assets/img/default/icon-star.png"></a></li>
            <li class="age"><?php echo Utilities::englishTime($photo['dateTaken'], 'Taken'); ?></li>
            <li class="permission <?php echo Utilities::englishPermission($photo['permission']); ?>"><?php echo Utilities::englishPermission($photo['permission']); ?></li>
          </ul>
          <!--(<a href="/photo/<?php echo $photo['id']; ?>/delete" class="photo-delete">delete</a>) -->
          <!--<?php if(!empty($photo['tags'])) { ?><?php echo implode(',', $photo['tags']); ?><?php } ?>-->
        </li>
      <?php } ?>
    </ul>
  <?php } else { ?>
    <h2>You haven't uploaded any photos yet</h2>
  <?php } ?>
</div>
