<div id="photo">
  <div class="info">
    <ul>
      <li class="date"><?php echo Utility::dateLong($photo['dateTaken']); ?></li>
      <li class="heart">Favorited 9 times &amp; 3 comments - <a href="#comments">see all</a></li>
      <?php if(!empty($photo['tags'])) { ?>
        <li class="tags"><?php echo Utility::tagsAsLinks($photo['tags']); ?></li>
      <?php } ?>
      <?php if(!empty($photo['latitude']) && !empty($photo['latitude'])) { ?>
        <li class="globe"><?php echo $photo['latitude']; ?>, <?php echo $photo['longitude']; ?></li>
      <?php } ?>
    </ul>
    <div class="comment-form">
      <form method="post" action="/action/photo/<?php echo $photo['id']; ?>">
        <textarea rows="1" cols="50" name="value" class="comment"></textarea>
        <input type="hidden" name="type" value="comment">
        <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
        <div class="buttons">
          <button type="submit">Comment</button>
          <?php if(count($photo['actions']) > 0) { ?>
            <div class="see-all"><a href="#comments">show <?php echo sprintf('%d %s', count($photo['actions']), Utility::plural(count($photo['actions']), 'comment')); ?><a/></div>
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
  </div>

  <?php if(count($photo['actions']) > 0) { ?>
    <a name="comments"></a>
    <ol>
      <?php foreach($photo['actions'] as $action) { ?>
        <li class="action-container-<?php echo $action['id']; ?>">
          <?php if($action['type'] == 'comment') { ?>
            <?php echo $action['value']; ?><br>(<a href="/action/<?php echo $action['id']; ?>/delete" class="action-delete">delete</a>)
          <?php } else { ?>
            Favorited<br>(<a href="/action/<?php echo $action['id']; ?>/delete" class="action-delete">delete</a>)
          <?php } ?>
        </li>
      <?php } ?>
    </ol>
  <?php } ?>

  <?php if(User::isOwner()) { ?>
    <form method="post" action="/action/photo/<?php echo $photo['id']; ?>">
      <textarea rows="5" cols="50" name="value"></textarea>
      <input type="hidden" name="type" value="comment">
      <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
      <br>
      <button type="submit" class="button pill icon pen">Comment</button>&nbsp;
    </form>
    <form method="post" action="/action/photo/<?php echo $photo['id']; ?>">
      <input type="hidden" name="value" value="1">
      <input type="hidden" name="type" value="favorite">
      <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
      <br>
      <button type="submit" class="button pill icon star">Favorite</button>&nbsp;
    </form>
    <form method="post">
      <h3>Tags</h3>
      <input type="text" name="tags" value="<?php echo implode(',', $photo['tags']); ?>">
      <button type="submit" class="button pill icon tag">Update</button>
    </form>
  <?php } ?>
</div>
