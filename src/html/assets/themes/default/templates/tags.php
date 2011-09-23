<h1 class="audible">Your Tags</h1>
<?php if(empty($tags)) { ?>
  You don't have any tags yet.
<?php } else { ?>
  <ol class="tag-cloud">
    <?php foreach($tags as $tag) { ?>
    <li class="size-<?php Utility::safe($tag['weight']); ?>">
      <span class="audible"><?php Utility::safe($tag['count']); ?> photos are tagged with</span>
      <a href="<?php Url::photosView("tags-{$tag['id']}"); ?>" title="<?php Utility::safe($tag['count']); ?> photos"><?php Utility::safe($tag['id']); ?></a>
    </li>
    <?php } ?>
  </ol>
<?php } ?>
