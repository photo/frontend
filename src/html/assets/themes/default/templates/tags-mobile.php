<div data-role="page" data-add-back-btn="true" id="Gallery1" class="gallery-page" data-theme="c">
  <div data-role="header" class="photoheader" data-theme="c">
    <a href="/" data-icon="home" class="ui-btn-right" rel="external">Home</a>
  </div>
	<div data-role="content">
    <?php if(empty($tags)) { ?>
      Sorry, no photos have been tagged.
    <?php } else { ?>
      <ul data-role="listview" data-inset="true">
        <?php foreach($tags as $tag) { ?>
          <li class="size-<?php $this->utility->safe($tag['weight']); ?>">
            <a href="<?php $this->url->photosView("tags-{$tag['id']}"); ?>" title="<?php $this->utility->safe($tag[$tagField]); ?> photos">
              <?php $this->utility->safe($tag['id']); ?>
            </a>
          </li>
        <?php } ?>
      </ol>
    <?php } ?>
  </div>
	<div data-role="footer" data-theme="c">
    <h4>The OpenPhoto Project &#169; <?php echo date('Y'); ?></h4>
	</div>
</div>
