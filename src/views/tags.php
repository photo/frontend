<?php if(empty($tags)) { ?>
  You don't have any tags yet.
<?php } else { ?>
  <ul class="tag-cloud">
    <?php foreach($tags as $tag) { ?>
    <li>
      <a href="/photos/tags-<?php echo $tag['id']; ?>" class="tag-<?php echo $tag['weight']; ?>" title="<?php echo $tag['count']; ?> photos">
        <?php echo $tag['id']; ?>
      </a>
    </li>
    <?php } ?>
  </ul>
<?php } ?>
