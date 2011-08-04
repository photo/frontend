<div id="photo">
  <div class="info">
    <div>
      <ul>
        <li class="date"><?php echo Utility::dateLong($photo['dateTaken']); ?></li>
        <li class="heart"><?php echo count($photo['actions']); ?> favorites &amp; comments - <a href="#comments">see all</a></li>
        <?php if(!empty($photo['tags'])) { ?>
          <li class="tags"><?php echo Utility::tagsAsLinks($photo['tags']); ?></li>
        <?php } ?>
        <?php if(!empty($photo['latitude']) && !empty($photo['latitude'])) { ?>
          <li class="globe"><?php echo $photo['latitude']; ?>, <?php echo $photo['longitude']; ?> - <a href="#map">see map</a></li>
        <?php } ?>
      </ul>
    </div>
    <div class="next-previous">
      <?php if(isset($photo['nextprevious']['previous'])) { ?>
        <div class="previous"><a href="/photo/<?php echo $photo['nextprevious']['previous']['id']; ?>"><img src="<?php echo $photo['nextprevious']['previous']['path50x50xCR']; ?>"></a></div>
      <?php } ?>
      <?php if(isset($photo['nextprevious']['next'])) { ?>
        <div class="next"><a href="/photo/<?php echo $photo['nextprevious']['next']['id']; ?>"><img src="<?php echo $photo['nextprevious']['next']['path50x50xCR']; ?>"></a></div>
      <?php } ?>
    </div>
    <div class="comment-form">
      <form method="post" action="/action/photo/<?php echo $photo['id']; ?>">
      <textarea rows="1" cols="50" name="value" class="comment" <?php if(!User::isLoggedIn()) { ?> disabled="true" <?php } ?>></textarea>
        <input type="hidden" name="type" value="comment">
        <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
        <div class="buttons">
          <?php if(User::isLoggedIn()) { ?>
            <button type="submit">Comment</button>
            <?php if(count($photo['actions']) > 0) { ?>
              <div class="see-all"><a href="#comments">show <?php echo sprintf('%d %s', count($photo['actions']), Utility::plural(count($photo['actions']), 'comment')); ?></a></div>
            <?php } ?>
          <?php } else { ?>
            <button type="button" class="login">Sign in to comment</button>
          <?php } ?>
        </div>
      </form>
    </div>
    <!--<form method="post" action="/action/photo/<?php echo $photo['id']; ?>">
      <input type="hidden" name="value" value="1">
      <input type="hidden" name="type" value="favorite">
      <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
      <br>
      <button type="submit">Favorite</button>&nbsp;
    </form>-->
    <br clear="all">
  </div>
  <div class="picture">
      <img src="<?php echo $photo['path960x960']; ?>" style="width:<?php echo $photo['thisWidth']; ?>px; height:<?php echo $photo['thisHeight']; ?>px;">
      <?php if(!empty($photo['title'])) { ?>
        <div class="title" style="width:<?php echo $photo['thisWidth']; ?>px;"><?php echo $photo['title']; ?></div>
      <?php } ?>
      <?php if(!empty($photo['description'])) { ?>
        <div class="description"style="width:<?php echo $photo['thisWidth']; ?>px;"><?php echo $photo['description']; ?></div>
      <?php } ?>
  </div>

  <div class="meta-content">
    <div>
      <?php if(count($photo['actions']) > 0) { ?>
        <a name="comments"></a>
        <ol class="comments">
          <?php foreach($photo['actions'] as $action) { ?>
            <li class="action-container-<?php echo $action['id']; ?>">
              <img src="<?php echo User::getAvatarFromEmail(40, $action['email']); ?>" class="avatar">
              <?php if($action['type'] == 'comment') { ?>
                <?php echo $action['value']; ?>
              <?php } else { ?>
                Favorited
              <?php } ?>
              <div class="date"><?php echo Utility::dateLong($action['datePosted']); ?></div>
            </li>
          <?php } ?>
        </ol>
      <?php } ?>
    </div>
    <div>
      <?php if(!empty($photo['latitude']) && !empty($photo['longitude'])) { ?>
        <a name="map"></a>
        <img src="<?php echo Utility::staticMapUrl($photo['latitude'], $photo['longitude'], 14, '450x150'); ?>" class="map">
      <?php } ?>
      <ul class="exif">
        <?php foreach(array('exifCameraMake' => 'Camera make', 'exifCameraModel' => 'Camera model') as $key => $value) { ?>
          <?php if(!empty($photo[$key])) { ?>
            <li><?php echo $value; ?>: <?php echo $photo[$key]; ?></li>
          <?php } ?>
        <?php } ?>
      </ul>
    </div>
    <br clear="all">
  </div>

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
            <ol class="comments">
              <?php foreach($photo['actions'] as $action) { ?>
                <li class="action-container-<?php echo $action['id']; ?>">
                  <img src="<?php echo User::getAvatarFromEmail(40, $action['email']); ?>" class="avatar">
                  <?php if($action['type'] == 'comment') { ?>
                    <?php echo $action['value']; ?><br><a href="/action/<?php echo $action['id']; ?>/delete" class="button delete action-delete">delete</a>
                  <?php } else { ?>
                    Favorited<br><a href="/action/<?php echo $action['id']; ?>/delete" class="button delete action-delete">delete</a>
                  <?php } ?>
                  <div class="date"><?php echo Utility::dateLong($action['datePosted']); ?></div>
                </li>
              <?php } ?>
            </ol>
          </div>
        <?php } ?>
      </div>
      <div class="delete">
        <form method="post" action="/photo/delete/<?php echo $photo['id']; ?>">
          <button type="submit" class="delete photo-delete">Delete this photo</button>
        </form>
      </div>
      <br clear="all">
    </div>
  <?php } ?>
</div>
