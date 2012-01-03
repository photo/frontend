<div class="headerbar">
	Tags <!-- function bar for e.g. sharing function -->
</div>
<h1 class="audible">Your Tags</h1>
<?php if(empty($tags)) { ?>
  Sorry, no photos have been tagged.
<?php } else { ?>
  <div class="row">
    <div class="span16">
      <ol class="tag-cloud">
        <?php foreach($tags as $tag) { ?>
        <li class="size-<?php $this->utility->safe($tag['weight']); ?>">
          <a href="<?php $this->url->photosView("tags-{$tag['id']}"); ?>" title="<?php $this->utility->safe($tag['count']); ?> photos"><?php $this->utility->safe($tag['id']); ?></a>
        </li>
        <?php } ?>
      </ol>
    </div>
  </div>
<?php } ?>
