<div>
  <div class="photo-wrapper">
    <div class="photo-column">
      <h1><?php Utility::safe($photo['title']); ?></h1>
      <p class="description"><?php Utility::safe($photo['description']); ?></p>
      <img class="photo" width="<?php Utility::safe($photo['thisWidth']); ?>" height="<?php Utility::safe($photo['thisHeight']); ?>" src="<?php Utility::photoUrl($photo, getConfig()->get('photoSizes')->detail); ?>" alt="My last latte at Yahoo!">
    </div>
    <div class="sidebar">
      <div class="image-pagination">
        <?php if(!empty($photo['previous'])) { ?>
          <div class="previous">
            <a href="/photo/<?php Utility::safe($photo['previous']['id']); ?>" style="background:url(<?php Utility::photoUrl($photo['previous'], getConfig()->get('photoSizes')->nextPrevious); ?>) top left no-repeat;"><span class="audible">Go to previous photo</span></a>
          </div>
        <?php } else { ?>
          <div class="empty"></div>
        <?php } ?>
        <div class="next">
          <?php if(!empty($photo['next'])) { ?>
            <a href="/photo/<?php Utility::safe($photo['next']['id']); ?>" style="background:url(<?php Utility::photoUrl($photo['next'], getConfig()->get('photoSizes')->nextPrevious); ?>) top left no-repeat"><span class="audible">Go to next photo</span></a>
          <?php } ?>
        </div>
      </div>
      <ul class="meta">
        <li class="date">Taken on <?php Utility::dateLong($photo['dateTaken']); ?></li>
        <li class="heart"><?php echo count($photo['actions']); ?> favorites &amp; comments - <a href="#comments" class="action-jump-click">see all</a></li>
        <li class="tags"><?php Utility::tagsAsLinks($photo['tags']); ?></li>
        <?php if(!empty($photo['latitude']) && !empty($photo['latitude'])) { ?>
          <li class="location">
            <?php Utility::safe($photo['latitude']); ?>, <?php Utility::safe($photo['longitude']); ?>
            <img src="<?php Utility::staticMapUrl($photo['latitude'], $photo['longitude'], 14, '225x150'); ?>" class="map">
          </li>
        <?php } ?>
        <li class="exif">
          <ul>
            <?php foreach(array('exifCameraMake' => 'Camera make', 'exifCameraModel' => 'Camera model') as $key => $value) { ?>
              <?php if(!empty($photo[$key])) { ?>
                <li><?php Utility::safe($value); ?>: <?php Utility::safe($photo[$key]); ?></li>
              <?php } ?>
            <?php } ?>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
<a name="comments"></a>
<div class="comment-form">
  <form method="post" action="/action/photo/<?php Utility::safe($photo['id']); ?>">
    <textarea rows="5" cols="50" name="value" class="comment" <?php if(!User::isLoggedIn()) { ?>disabled="true"<?php } ?> ></textarea>
    <input type="hidden" name="type" value="comment">
    <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
    <div class="buttons">
    <?php if(User::isLoggedIn()) { ?>
      <button type="submit">Leave a comment</button>
    <?php } else { ?>
      <button type="button" class="login-click">Sign in to comment</button>
    <?php } ?>
    </div>
  </form>
  <form method="post" action="/action/photo/<?php Utility::safe($photo['id']); ?>">
    <input type="hidden" name="value" value="1">
    <input type="hidden" name="type" value="favorite">
    <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
    <br>
    <button type="submit">Favorite</button>
  </form>
</div>
<?php if(count($photo['actions']) > 0) { ?>
  <a name="comments"></a>
  <ul class="comments">
    <?php foreach($photo['actions'] as $action) { ?>
      <li class="action-container-<?php echo $action['id']; ?>">
        <img src="<?php echo User::getAvatarFromEmail(40, $action['email']); ?>" class="avatar">
        <?php if($action['type'] == 'comment') { ?>
          <?php echo $action['value']; ?>
        <?php } else { ?>
          Favorited
        <?php } ?>
        <ul class="meta">
          <li class="date"><?php echo Utility::dateLong($action['datePosted']); ?></li>
        </ul>
      </li>
    <?php } ?>
  </ul>
<?php } ?>

<?php if(User::isOwner()) { ?>
  <div class="owner">
    <h3>This photo belongs to you</h3>
    <div>
      <div class="detail-form">
        <form method="post">
          <label>Title</label>
          <input type="text" name="title" value="<?php echo $photo['title']; ?>">

          <label>Description</label>
          <textarea name="description"><?php echo $photo['description']; ?></textarea>

          <label>Tags</label>
          <input type="text" name="tags" value="<?php echo implode(',', $photo['tags']); ?>">

          <label>Latitude</label>
          <input type="text" name="latitude" value="<?php echo $photo['latitude']; ?>">

          <label>Longitude</label>
          <input type="text" name="longitude" value="<?php echo $photo['longitude']; ?>">

          <button type="submit">Update photo</button>
        </form>
      </div>
      <?php if(count($photo['actions']) > 0) { ?>
        <div class="manage-comments">
          <label>Manage comments</label>
          <ul class="comments">
            <?php foreach($photo['actions'] as $action) { ?>
              <li class="action-container-<?php echo $action['id']; ?>">
                <img src="<?php echo User::getAvatarFromEmail(40, $action['email']); ?>" class="avatar">
                <?php if($action['type'] == 'comment') { ?>
                  <?php echo $action['value']; ?><br><a href="/action/<?php echo $action['id']; ?>/delete" class="button delete action-delete-click">delete</a>
                <?php } else { ?>
                  Favorited<br><a href="/action/<?php echo $action['id']; ?>/delete" class="button delete action-delete-click">delete</a>
                <?php } ?>
                <ul class="meta">
                  <li class="date"><?php echo Utility::dateLong($action['datePosted']); ?></li>
                </ul>
              </li>
            <?php } ?>
          </ul>
        </div>
      <?php } ?>
    </div>
    <div class="delete">
      <form method="post" action="/photo/delete/<?php echo $photo['id']; ?>">
        <button type="submit" class="delete photo-delete-click">Delete this photo</button>
      </form>
    </div>
    <br clear="all">
  </div>
<?php } ?>
