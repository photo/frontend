<?php if(empty($tags)) { ?>
  You don't have any tags yet.
<?php } else { ?>
  <ul>
    <?php foreach($tags as $tag) { ?>
    <li><a href="/photos/tags-<?php echo $tag['id']; ?>"><?php echo $tag['id']; ?></a> (<?php echo $tag['count']; ?>)</li>
    <?php } ?>
  </ul>
<?php } ?>
