<div data-role="page" data-add-back-btn="true" id="Gallery1" class="gallery-page" data-theme="c">
	<div data-role="header" class="photoheader" data-theme="c"></div>
	<div data-role="content">	
    <?php if(empty($tags)) { ?>
      Sorry, no photos have been tagged.
    <?php } else { ?>
      <ul data-role="listview" data-inset="true">
        <?php foreach($tags as $tag) { ?>
          <li class="size-<?php Utility::safe($tag['weight']); ?>">
            <span class="audible"><?php Utility::safe($tag['count']); ?> photos are tagged with</span>
            <a href="<?php Url::photosView("tags-{$tag['id']}"); ?>" title="<?php Utility::safe($tag['count']); ?> photos">
              <?php Utility::safe($tag['id']); ?>
            </a>
          </li>
        <?php } ?>
      </ol>
    <?php } ?>
  </div>
	<div data-role="footer" data-theme="c">
		<h4>The OpenPhoto Project &#169; 2011</h4>
	</div>
</div>
