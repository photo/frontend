<?php $thumbnailSize = isset($_GET['size']) ? $_GET['size'] : '280x186xCR'; ?>
<?php if($photos[0]['totalRows'] > 0) { ?>
  <?php $this->theme->display('partials/pagination.php', $pages); ?>
  <ul class="photo-grid <?php $this->utility->safe("size{$thumbnailSize}"); ?>">
    <?php foreach($photos as $photo) { ?>
      <li class="grid-item unpinned id-<?php $this->utility->safe($photo['id']); ?> pin-out">
        <a href"#" class="pin pin-click" data-id="<?php $this->utility->safe($photo['id']); ?>"></a>
        <a href="<?php $this->url->photoView($photo['id'], $options); ?>">
          <img src="<?php $this->url->photoUrl($photo, $thumbnailSize); ?>" alt="<?php $this->utility->safe($photo['title']); ?>" class="thumbnail pin-over" />
          <ul class="meta">
            <li class="age"><?php $this->utility->timeAsText($photo['dateTaken'], 'Taken'); ?></li>
            <li class="permission"><span class="<?php $this->utility->permissionAsText($photo['permission']); ?>" title="Photo is <?php $this->utility->permissionAsText($photo['permission']); ?>"></li>
            <?php if(isset($photo['tags']) && !empty($photo['tags'])) { ?><li class="tags"><span title="Photo has tags"></span></li><?php } ?>
            <?php if(isset($photo['latitude']) && !empty($photo['latitude'])) { ?><li class="geo"><span title="Photo has geo informations"></span></li><?php } ?>
          </ul>
        </a>
      </li>
    <?php } ?>
  </ul>
  <br clear="all">
  <span class="paginationbottom"><?php $this->theme->display('partials/pagination.php', array_merge($pages, array('labelPosition' => 'bottom'))); ?></span>
<?php } else { ?>
  <?php if($this->user->isOwner()) { ?>
    <a href="<?php $this->url->photoUpload(); ?>" class="link" title="Start uploading now!"><img src="<?php $this->theme->asset('image', 'front.png'); ?>" class="front" /></a>
    <h1>There don't seem to be any photos. You should <a href="<?php $this->url->photosUpload(); ?>" class="link">upload</a> some.</h1>
    <p>
      If you're searching for photos then there aren't any which match your query.
    </p>
  <?php } else { ?>
	<img src="<?php $this->theme->asset('image', 'front-general.png'); ?>" class="front" />
    <h1>No photos to show.</h1>
    <p>
      This could be because the user hasn't uploaded any photos yet or you've searched for photos that do not exist.
    </p>
  <?php } ?>
<?php } ?>
