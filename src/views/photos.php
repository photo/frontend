<ul>
  <?php foreach($photos as $photo) { ?>
  <li>Photo <?php echo $photo->id; ?> has url <?php echo $photo->urlOriginal; ?></li>
  <?php } ?>
</ul>
