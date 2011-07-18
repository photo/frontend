<h2>Recently shared photos</h2>

<div id="photos">
  <ul class="grid">
    <?php foreach($photos as $photo) { ?>
      <li class="photo-container-<?php echo $photo['id']; ?>">
        (<a href="/photo/<?php echo $photo['id']; ?>/delete" class="photo-delete">delete</a>)
        <br>
        <a href="/photo/<?php echo $photo['id']; ?>"><img src="<?php echo Photo::generateUrlPublic($photo, 200, 200); ?>"></a>
        <br>
        Tags:
        <?php if(!empty($photo['tags'])) { ?><?php echo implode(',', $photo['tags']); ?><?php } ?>
        <?php if($photo['dateTaken']) { ?>
          <br/>
          Taken: <?php echo date('D M j, Y', $photo['dateTaken']); ?>
        <?php } ?>
      </li>
    <?php } ?>
  </ul>
</div>

