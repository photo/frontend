<div>
  <div class="photo-wrapper">
    <div class="photo-column">
      <h1><?php Utility::safe($photo['title']); ?></h1>
      <p class="description"><?php Utility::safe($photo['description']); ?></p>
      <img class="photo" width="<?php Utility::safe($photo['thisWidth']); ?>" height="<?php Utility::safe($photo['thisHeight']); ?>" src="<?php Url::photoUrl($photo, getConfig()->get('photoSizes')->detail); ?>" alt="<?php Utility::safe($photo['title']); ?>">
    </div>
    <div class="sidebar">
      <div class="image-pagination">
        <?php if(!empty($photo['previous'])) { ?>
          <div class="previous">
            <a href="<?php Url::photoView($photo['previous']['id'], $options); ?>" style="background:url(<?php Url::photoUrl($photo['previous'], getConfig()->get('photoSizes')->nextPrevious); ?>) top left no-repeat;"><span class="audible">Go to previous photo</span></a>
          </div>
        <?php } else { ?>
          <div class="empty"></div>
        <?php } ?>
        <div class="next">
          <?php if(!empty($photo['next'])) { ?>
            <a href="<?php Url::photoView($photo['next']['id'], $options); ?>" style="background:url(<?php Url::photoUrl($photo['next'], getConfig()->get('photoSizes')->nextPrevious); ?>) top left no-repeat"><span class="audible">Go to next photo</span></a>
          <?php } ?>
        </div>
      </div>
      <ul class="meta">
        <li class="date"><?php Utility::dateLong($photo['dateTaken']); ?></li>
        <li class="heart"><?php echo count($photo['actions']); ?> favorites &amp; comments - <a href="#comments" class="action-jump-click">see all</a></li>
        <li class="tags"><?php Url::tagsAsLinks($photo['tags']); ?></li>
        <li class="license"><?php if(isset($photo['license'])) Utility::licenseLong($photo['license']); ?></li>
        <?php if(!empty($photo['latitude']) && !empty($photo['latitude'])) { ?>
          <li class="location">
            <?php Utility::safe($photo['latitude']); ?>, <?php Utility::safe($photo['longitude']); ?>
            <img src="<?php Utility::staticMapUrl($photo['latitude'], $photo['longitude'], 14, '225x150'); ?>" class="map">
          </li>
        <?php } ?>
        <li class="exif">
          <ul>
            <?php foreach(array('exifCameraMake' => 'Camera make: %s',
                                        'exifCameraModel' => 'Camera model: %s',
                                        'exifFNumber' => 'Av: f/%1.0F',
                                        'exifExposureTime' => 'Tv: %s',
                                        'exifISOSpeed' => 'ISO: %d',
                                        'exifFocalLength' => 'Focal Length: %1.0fmm') as $key => $value) { ?>
              <?php if(!empty($photo[$key])) { ?>
                <li><?php printf($value, Utility::safe($photo[$key], false)); ?></li>
              <?php } ?>
            <?php } ?>
          </ul>
        </li>
      </ul>
      <?php if(User::isOwner()) { ?>
        <a href="<?php Url::photoEdit($photo['id']); ?>" class="button photo-edit-click">Edit this photo</a>
      <?php } ?>
    </div>
  </div>
</div>
<a name="comments"></a>
<?php if(count($photo['actions']) > 0) { ?>
  <ul class="comments">
    <?php foreach($photo['actions'] as $action) { ?>
      <li class="action-container-<?php echo $action['id']; ?>">
        <img src="<?php echo User::getAvatarFromEmail(40, $action['email']); ?>" class="avatar">
        <?php if($action['type'] == 'comment') { ?>
          <?php echo $action['value']; ?>
        <?php } else { ?>
          Marked this photo as a favorite
        <?php } ?>
        <div class="date">
          <?php echo Utility::dateLong($action['datePosted']); ?>
          <?php if(User::isOwner()) { ?>
            <form method="post" action="<?php Url::actionDelete($action['id']); ?>">
              <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
              (<a href="<?php Url::actionDelete($action['id']); ?>" class="action-delete-click">delete</a>)
            </form>
          <?php } ?>
        </div>
      </li>
    <?php } ?>
  </ul>
<?php } ?>
<div class="comment-form">
  <form method="post" action="<?php Url::actionCreate($photo['id'], 'photo'); ?>">
    <textarea rows="5" cols="50" name="value" class="comment" <?php if(!User::isLoggedIn()) { ?>disabled="true"<?php } ?> ></textarea>
    <input type="hidden" name="type" value="comment">
    <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
    <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
    <div class="buttons">
    <?php if(User::isLoggedIn()) { ?>
      <button type="submit">Leave a comment</button>
    <?php } else { ?>
      <button type="button" class="login-click">Sign in to comment</button>
    <?php } ?>
    </div>
  </form>
  <?php if(User::isLoggedIn()) { ?>
    or
    <form method="post" action="<?php Url::actionCreate($photo['id'], 'photo'); ?>">
      <input type="hidden" name="value" value="1">
      <input type="hidden" name="type" value="favorite">
      <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
      <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
      <br>
      <button type="submit">Favorite</button>
    </form>
  <?php } ?>
</div>
