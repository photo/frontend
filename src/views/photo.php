<div id="photo">
  <div class="info">
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
    <div class="comment-form">
      <form method="post" action="/action/photo/<?php echo $photo['id']; ?>">
        <textarea rows="1" cols="50" name="value" class="comment"></textarea>
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
                <?php echo $action['value']; ?><br>(<a href="/action/<?php echo $action['id']; ?>/delete" class="action-delete">delete</a>)
              <?php } else { ?>
                Favorited<br>(<a href="/action/<?php echo $action['id']; ?>/delete" class="action-delete">delete</a>)
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
          <li><?php echo $value; ?>: <?php echo $photo[$key]; ?></li>
        <?php } ?>
      </ul>
    </div>
    <br clear="all">
  </div>

  <?php if(User::isOwner()) { ?>
    <div class="owner">
      <h3>This photo belongs to you</h3>
      
      <form method="post">
        <h3>Tags</h3>
        <input type="text" name="tags" value="<?php echo implode(',', $photo['tags']); ?>">
        <button type="submit" class="button pill icon tag">Update</button>
      </form>
    </div>
  <?php } ?>
</div>
