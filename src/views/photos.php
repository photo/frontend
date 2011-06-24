<div id="photos">
  <ul class="grid">
    <?php foreach($photos as $photo) { ?>
    <li class="photo-container-<?php echo $photo['id']; ?>">
      (<a href="/photo/<?php echo $photo['id']; ?>/delete" class="photo-delete">delete</a>)
      <br>
      <a href="/photo/<?php echo $photo['id']; ?>"><img src="<?php echo Photo::generateUrlPublic($photo, 200, 200); ?>"></a>
      Tags:
      <?php foreach((array)$photo['tags'] as $tag) { ?>
        
      <?php } ?>
      <br/>
      Taken: <?php echo date('D M j, Y', $photo['dateTaken']); ?>
    </li>
    <?php } ?>
  </ul>
</div>
