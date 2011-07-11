<ul>
  <?php foreach($sizes as $size => $url) { ?>
    <li><a href="<?php echo sprintf('/photo/%s/%s', $photo['id'], $size); ?>"><?php echo $size; ?></a></li>
  <?php } ?>
</ul>
<form method="post">
  <label>Tags<label>
  <input type="text" name="tags" value="<?php echo implode(',', $photo['tags']); ?>">
  <button type="submit" class="button pill icon pen">Update</button>
</form>
<img src="<?php echo $photo['displayUrl']; ?>">
