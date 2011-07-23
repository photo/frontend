<ul>
  <?php foreach($sizes as $size => $url) { ?>
    <li><a href="<?php echo sprintf('/photo/%s/%s', $photo['id'], $size); ?>"><?php echo $size; ?></a></li>
  <?php } ?>
</ul>
<form method="post">
  <h3>Tags</h3>
  <input type="text" name="tags" value="<?php echo implode(',', $photo['tags']); ?>">
  <button type="submit" class="button pill icon tag">Update</button>
</form>
<br>
<img src="<?php echo $photo['displayUrl']; ?>">
<br>
<form method="post" action="/photo/<?php echo $photo['id']; ?>/action">
  <textarea rows="5" cols="50" name="value"></textarea>
  <input type="hidden" name="type" value="comment">
  <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
  <br>
  <button type="submit" class="button pill icon pen">Comment</button>&nbsp;
</form>
<form method="post" action="/photo/<?php echo $photo['id']; ?>/action">
  <input type="hidden" name="value" value="1">
  <input type="hidden" name="type" value="favorite">
  <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
  <br>
  <button type="submit" class="button pill icon star">Favorite</button>&nbsp;
</form>
<?php if(count($photo['actions']) > 0) { ?>
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
