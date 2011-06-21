<ul>
  <?php foreach($sizes as $size => $url) { ?>
    <li><a href="<?php echo sprintf('/photo/%s/%s', $photo['id'], $size); ?>"><?php echo $size; ?></a></li>
  <?php } ?>
</ul>

<img src="<?php echo $photo['displayUrl']; ?>">
