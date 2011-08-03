<div id="photos">
  <?php if($photos[0]['totalRows'] > 0) { ?>
    <?php getTemplate()->display('partial/pagination.php', $pagination); ?>
    <ul class="grid">
      <?php foreach($photos as $photo) { ?>
        <li class="photo-container-<?php echo $photo['id']; ?>">
          <a href="/photo/<?php echo $photo['id']; ?>"><img src="<?php echo $photo['path200x200xCR']; ?>"></a>
          <span class="age"><?php echo Utility::englishTime($photo['dateTaken'], 'Taken'); ?></span>
          <span class="permission <?php echo Utility::englishPermission($photo['permission']); ?>"><?php echo Utility::englishPermission($photo['permission']); ?></span>
          <ul class="options">
            <?php if(!empty($photo['tags'])) { ?><li><img src="/assets/img/default/icon-tags.png" alt="This photo has tags"></li><?php } ?>
            <?php if(!empty($photo['latitude'])) { ?><li><img src="/assets/img/default/icon-globe.png" alt="This photo has location information"></li><?php } ?>
            <!--<li><a href="#"><img src="/assets/img/default/icon-add.png"></a></li>
            <li><a href="#"><img src="/assets/img/default/icon-star.png"></a></li>-->
          </ul>
          <!--(<a href="/photo/<?php echo $photo['id']; ?>/delete" class="photo-delete">delete</a>) -->
          <!--<?php if(!empty($photo['tags'])) { ?><?php echo implode(',', $photo['tags']); ?><?php } ?>-->
        </li>
      <?php } ?>
    </ul>
    <br clear="all">
    <?php getTemplate()->display('partial/pagination.php', array_merge($pagination, array('labelPosition' => 'bottom'))); ?>
  <?php } else { ?>
    <h2>You haven't uploaded any photos yet</h2>
  <?php } ?>
</div>
