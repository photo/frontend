<div class="headerbar">
  Photos <!-- function bar for e.g. sharing function -->
</div>

<section class="photo-column unpinned id-<?php $this->utility->safe($photo['id']); ?> pin-out">
  <a href"#" class="pin pin-click" data-id="<?php $this->utility->safe($photo['id']); ?>"></a>
  <div>
    <img class="photo pin-over" width="<?php $this->utility->safe($photo['photo'.$this->config->photoSizes->detail][1]); ?>" height="<?php $this->utility->safe($photo['photo'.$this->config->photoSizes->detail][2]); ?>" src="<?php $this->url->photoUrl($photo, $this->config->photoSizes->detail); ?>" alt="<?php $this->utility->safe($photo['title']); ?>">
  </div>
  <h1><?php $this->utility->safe($photo['title']); ?></h1>
  <p class="description"><?php $this->utility->safe($photo['description']); ?></p>
  <?php if(count($photo['actions']) > 0) { ?>
    <ul class="comments" id="comments">
      <?php foreach($photo['actions'] as $action) { ?>
        <li class="action-container-<?php $this->utility->safe($action['id']); ?>">
          <img src="<?php $this->utility->safe($this->user->getAvatarFromEmail(40, $action['email'])); ?>" class="avatar">
          <div>
            <strong><?php $this->utility->getEmailHandle($action['email']); ?> <small>(<?php $this->utility->safe($this->utility->dateLong($action['datePosted'])); ?>)</small></strong>
            <?php if($action['type'] == 'comment') { ?>
              <span><?php $this->utility->safe($action['value']); ?></span>
            <?php } else { ?>
              <span>Marked this photo as a favorite.</span>
            <?php } ?>
            <span class="date">
              <?php if($this->user->isOwner()) { ?>
                <form method="post" action="<?php $this->url->actionDelete($action['id']); ?>">
                  <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
                  <a href="<?php $this->url->actionDelete($action['id']); ?>" data-id="<?php $this->utility->safe($action['id']); ?>" class="action-delete-click"><span></span>Delete comment</a>
                </form>
              <?php } ?>
            </span>
          </div>
        </li>
      <?php } ?>
    </ul>
  <?php } ?>
  <div class="comment-form">
    <form method="post" action="<?php $this->url->actionCreate($photo['id'], 'photo'); ?>">
      <textarea rows="5" cols="50" name="value" class="comment" <?php if(!$this->user->isLoggedIn()) { ?>disabled="true"<?php } ?> ></textarea>
      <input type="hidden" name="type" value="comment">
      <input type="hidden" name="targetUrl" value="<?php $this->utility->safe(sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'])); ?>">
      <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
      <div class="buttons">
      <?php if($this->user->isLoggedIn()) { ?>
        <button type="submit">Leave a comment</button>
      <?php } else { ?>
        <button type="button" class="login-click browserid">Sign in to comment</button>
      <?php } ?>
      </div>
    </form>
    <?php if($this->user->isLoggedIn()) { ?>
      <form method="post" action="<?php $this->url->actionCreate($photo['id'], 'photo'); ?>">
        <input type="hidden" name="value" value="1">
        <input type="hidden" name="type" value="favorite">
        <input type="hidden" name="targetUrl" value="<?php $this->utility->safe(sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'])); ?>">
        <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
        <button type="submit">Favorite</button>
      </form>
    <?php } ?>
  </div>
  
</section>
<aside class="sidebar">
  
  <p><strong>Discover more photos</strong></p>
  <ul class="image-pagination">
    <?php if(!empty($photo['previous'])) { ?>
    <li class="previous unpinned id-<?php $this->utility->safe($photo['previous'][0]['id']); ?> pin-out">
      <a href="#" class="pin pin-click" data-id="<?php $this->utility->safe($photo['previous'][0]['id']); ?>"></a>
      <a href="<?php $this->url->photoView($photo['previous'][0]['id'], $options); ?>" title="Go to previous photo" class="thumb">
        <img src="<?php $this->url->photoUrl($photo['previous'][0], $this->config->photoSizes->nextPrevious); ?>" alt="Go to previous photo" class="pin-over" />
        <span class="prev"><span></span></span>
        <span class="audible">Go to previous photo</span>
      </a>
    </li>
    <?php } else { ?>
      <li class="previous empty">
        <img src="<?php $this->theme->asset('image', 'empty.png'); ?>" alt="No previous photo" />
      </li>
    <?php } ?>
    <?php if(!empty($photo['next'])) { ?>
    <li class="next unpinned id-<?php $this->utility->safe($photo['next'][0]['id']); ?> pin-out">
      <a href="#" class="pin pin-click" data-id="<?php $this->utility->safe($photo['next'][0]['id']); ?>"></a>
      <a href="<?php $this->url->photoView($photo['next'][0]['id'], $options); ?>" title="Go to next photo" class="thumb">
        <img src="<?php $this->url->photoUrl($photo['next'][0], $this->config->photoSizes->nextPrevious); ?>" alt="Go to next photo" class="pin-over" />
        <span class="next"><span></span></span>
        <span class="audible">Go to next photo</span>
      </a>
    </li>
    <?php } else { ?>
      <li class="next empty">
        <img src="<?php $this->theme->asset('image', 'empty.png'); ?>" alt="No next photo" />
      </li>
    <?php } ?>
  </ul>

  <?php $this->plugin->invoke('renderPhotoDetail', $photo); ?>

  <p><strong>Photo details</strong></p>
  <ul class="meta">
    <li class="date"><span></span><?php $this->utility->dateLong($photo['dateTaken']); ?></li>
    <li class="heart"><span></span><strong><?php echo count($photo['actions']); ?></strong> <a href="#comments" class="action-jump-click" title="Jump to favorites &amp; comments">favorites &amp; comments</a></li>
    <?php if(isset($photo['tags']) && !empty($photo['tags'])) { ?>
      <li class="tags"><span></span><?php $this->url->tagsAsLinks($photo['tags']); ?></li>
    <?php } ?>
    <li class="license"><span></span>
      <?php if($this->utility->licenseLink($photo['license'], false)) { ?>
        <a rel="license" href="<?php $this->utility->licenseLink($photo['license']); ?>">
          <?php $this->utility->licenseLong($photo['license']); ?>
        </a>
      <?php } else { ?>
        <?php $this->utility->licenseLong($photo['license']); ?>
      <?php } ?>
    </li>
    <?php if(isset($photo['latitude']) && !empty($photo['latitude'])) { ?>
      <li class="location">
        <span></span>
        <a href="<?php $this->utility->mapLinkUrl($photo['latitude'], $photo['longitude'], 5); ?>"><?php $this->utility->safe($photo['latitude']); ?>, <?php $this->utility->safe($photo['longitude']); ?>
        <img src="<?php $this->utility->staticMapUrl($photo['latitude'], $photo['longitude'], 5, '255x150'); ?>" class="map"></a>
      </li>
    <?php } ?>
    <?php if(!empty($photo['exifCameraMake']) && !empty($photo['exifCameraMake'])) { ?>
    <li class="exif">
      <span></span>
      <ul>
        <?php foreach(array('exifCameraMake' => 'Camera make: <em>%s</em>',
          'exifCameraModel' => 'Camera model: <em>%s</em>',
          'exifFNumber' => 'Av: <em>f/%1.1F</em>',
          'exifExposureTime' => 'Tv: <em>%s</em>',
          'exifISOSpeed' => 'ISO: <em>%d</em>',
          'exifFocalLength' => 'Focal Length: %1.0fmm') as $key => $value) { ?>
            <?php if(!empty($photo[$key])) { ?>
            <li><?php printf($value, $this->utility->safe($photo[$key], false)); ?></li>
            <?php } ?>
          <?php } ?>
        </ul>
      </li>
    <?php } ?>
    <?php if(isset($photo['pathOriginal'])) { ?>
      <li class="original"><span></span><a href="<?php $this->utility->safe($photo['pathOriginal']); ?>">Download original</a></li>
    <?php } ?>
  </ul>
</aside>
<div style="clear:both;"></div>
