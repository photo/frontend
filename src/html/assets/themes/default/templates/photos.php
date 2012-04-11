<?php if($photos[0]['totalRows'] > 0) { ?>
  <?php $this->theme->display('partials/pagination.php', $pages); ?>
  <ul class="photo-grid grid-200">
    <?php foreach($photos as $photo) { ?>
      <li class="grid-item id-<?php $this->utility->safe($photo['id']); ?>">
        <a href="<?php $this->url->photoView($photo['id'], $options); ?>"><img src="<?php $this->url->photoUrl($photo, $this->config->photoSizes->thumbnail); ?>" alt="<?php $this->utility->safe($photo['title']); ?>"></a>
        <ul class="meta">
          <li class="age"><?php $this->utility->timeAsText($photo['dateTaken'], 'Taken'); ?></li>
          <li class="permission <?php $this->utility->permissionAsText($photo['permission']); ?>"><?php $this->utility->permissionAsText($photo['permission']); ?></li>
          <?php if(!empty($photo['tags'])) { ?><li class="tags"><img src="<?php $this->theme->asset('image', 'icon-tags.png'); ?>" alt="This photo has tags"></li><?php } ?>
          <?php if(!empty($photo['latitude'])) { ?><li class="geo"><img src="<?php $this->theme->asset('image', 'icon-globe.png'); ?>" alt="This photo has location information"></li><?php } ?>
        </ul>
      </li>
    <?php } ?>
  </ul>
  <br clear="all">
  <?php $this->theme->display('partials/pagination.php', array_merge($pages, array('labelPosition' => 'bottom'))); ?>
<?php } else { ?>
  <?php if($this->user->isOwner()) { ?>
    <h1>There don't seem to be any photos. You should <a href="<?php $this->url->photosUpload(); ?>">upload</a> some.</h1>
    <p>
      If you're searching for photos then there aren't any which match your query.
    </p>
  <?php } else { ?>
    <h1>No photos to show.</h1>
    <p>
      This could be because the user hasn't uploaded any photos yet or you've searched for photos that do not exist.
    </p>
  <?php } ?>
<?php } ?>
