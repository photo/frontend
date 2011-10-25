<h1 class="audible">Your Tags</h1>
<?php if(empty($tags)) { ?>
  Sorry, no photos have been tagged.
<?php } else { ?>
  <ol class="tag-cloud">
    <?php foreach($tags as $tag) { ?>
    <li class="size-<?php Utility::safe($tag['weight']); ?>">
      <a href="<?php Url::photosView("tags-{$tag['id']}"); ?>" title="<?php Utility::safe($tag[$tagField]); ?> photos"><?php Utility::safe($tag['id']); ?></a>
    </li>
    <?php } ?>
  </ol>
<?php } ?>
