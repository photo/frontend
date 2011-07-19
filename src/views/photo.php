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
<br>
<img src="<?php echo $photo['displayUrl']; ?>">
<?php if(count($photo['actions']) > 0) { ?>
  <ol>
    <?php foreach($photo['actions'] as $action) { ?>
      <li><?php echo $action['value']; ?></li>
    <?php } ?>
  </ol>
<?php } ?>
