<div>
  <div class="photo-wrapper">
    <div class="photo-column">
      <h1><?php $this->utility->safe($photo['title']); ?></h1>
      <p class="description"><?php $this->utility->safe($photo['description']); ?></p>
      <img class="photo" width="<?php $this->utility->safe($photo['photo'.$this->config->photoSizes->detail][1]); ?>" height="<?php $this->utility->safe($photo['photo'.$this->config->photoSizes->detail][2]); ?>" src="<?php $this->url->photoUrl($photo, $this->config->photoSizes->detail); ?>" alt="<?php $this->utility->safe($photo['title']); ?>">
    </div>
    <div class="sidebar">
      <div class="image-pagination">
        <?php if(!empty($photo['previous'])) { ?>
          <div class="previous">
            <a href="<?php $this->url->photoView($photo['previous'][0]['id'], $options); ?>" style="background:url(<?php $this->url->photoUrl($photo['previous'][0], $this->config->photoSizes->nextPrevious); ?>) top left no-repeat;"><span class="audible">Go to previous photo</span></a>
          </div>
        <?php } else { ?>
          <div class="empty"></div>
        <?php } ?>
        <?php if(!empty($photo['next'])) { ?>
          <div class="next">
            <a href="<?php $this->url->photoView($photo['next'][0]['id'], $options); ?>" style="background:url(<?php $this->url->photoUrl($photo['next'][0], $this->config->photoSizes->nextPrevious); ?>) top left no-repeat"><span class="audible">Go to next photo</span></a>
          </div>
        <?php } else { ?>
          <div class="empty"></div>
        <?php } ?>
      </div>
      <ul class="meta">
        <li class="date"><?php $this->utility->dateLong($photo['dateTaken']); ?></li>
        <li class="heart"><?php echo count($photo['actions']); ?> favorites &amp; comments - <a href="#comments" class="action-jump-click">see all</a></li>
        <li class="tags"><?php $this->url->tagsAsLinks($photo['tags']); ?></li>
        <?php if(isset($photo['pathOriginal'])) { ?>
          <li class="original"><span></span><a href="<?php $this->utility->safe($photo['pathOriginal']); ?>">Download original</a></li>
        <?php } ?>
        <?php if($this->utility->licenseLink($photo['license'], false)) { ?>
          <a rel="license" href="<?php $this->utility->licenseLink($photo['license']); ?>">
            <?php $this->utility->licenseLong($photo['license']); ?>
          </a>
        <?php } else { ?>
          <?php $this->utility->licenseLong($photo['license']); ?>
        <?php } ?>
        <?php if(!empty($photo['latitude']) && !empty($photo['latitude'])) { ?>
          <li class="location">
            <a href="<?php $this->utility->mapLinkUrl($photo['latitude'], $photo['longitude'], 5); ?>"><?php $this->utility->safe($photo['latitude']); ?>, <?php $this->utility->safe($photo['longitude']); ?>
            <img src="<?php $this->utility->staticMapUrl($photo['latitude'], $photo['longitude'], 5, '225x150'); ?>" class="map"></a>
          </li>
        <?php } ?>
        <li class="exif">
          <ul>
            <?php foreach(array('exifCameraMake' => 'Camera make: %s',
                                        'exifCameraModel' => 'Camera model: %s',
                                        'exifFNumber' => 'Av: f/%1.1F',
                                        'exifExposureTime' => 'Tv: %s',
                                        'exifISOSpeed' => 'ISO: %d',
                                        'exifFocalLength' => 'Focal Length: %1.0fmm') as $key => $value) { ?>
              <?php if(!empty($photo[$key])) { ?>
                <li><?php printf($value, $this->utility->safe($photo[$key], false)); ?></li>
              <?php } ?>
            <?php } ?>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
<a name="comments"></a>
<?php if(count($photo['actions']) > 0) { ?>
  <ul class="comments">
    <?php foreach($photo['actions'] as $action) { ?>
      <li class="action-container-<?php echo $action['id']; ?>">
        <img src="<?php echo $this->user->getAvatarFromEmail(40, $action['email']); ?>" class="avatar">
        <?php if($action['type'] == 'comment') { ?>
          <?php echo $action['value']; ?>
        <?php } else { ?>
          Marked this photo as a favorite
        <?php } ?>
        <div class="date">
          <?php echo $this->utility->dateLong($action['datePosted']); ?>
          <?php if($this->user->isOwner()) { ?>
            <form method="post" action="<?php $this->url->actionDelete($action['id']); ?>">
              <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
              (<a href="<?php $this->url->actionDelete($action['id']); ?>" class="action-delete-click" data-id="<?php $this->utility->safe($action['id']); ?>">delete</a>)
            </form>
          <?php } ?>
        </div>
      </li>
    <?php } ?>
  </ul>
<?php } ?>
<div class="comment-form">
  <form method="post" action="<?php $this->url->actionCreate($photo['id'], 'photo'); ?>">
    <textarea rows="5" cols="50" name="value" class="comment" <?php if(!$this->user->isLoggedIn()) { ?>disabled="true"<?php } ?> ></textarea>
    <input type="hidden" name="type" value="comment">
    <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
    <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
    <div class="buttons">
    <?php if($this->user->isLoggedIn()) { ?>
      <button type="submit">Leave a comment</button>
    <?php } else { ?>
      <button type="button" class="login-click browserid">Sign in to comment</button>
    <?php } ?>
    </div>
  </form>
  <?php if($this->user->isLoggedIn()) { ?>
    or
    <form method="post" action="<?php $this->url->actionCreate($photo['id'], 'photo'); ?>">
      <input type="hidden" name="value" value="1">
      <input type="hidden" name="type" value="favorite">
      <input type="hidden" name="targetUrl" value="<?php printf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
      <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
      <br>
      <button type="submit">Favorite</button>
    </form>
  <?php } ?>
</div>
