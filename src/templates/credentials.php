<?php if(count($credentials) > 0) { ?>
  <ul>
    <?php foreach($credentials as $credential) { ?>
      <li><?php Utility::safe($credential['name']); ?> (<a href="/oauth/<?php echo $credential['id']; ?>/delete" class="credential-delete-click">delete</a>)</li>
    <?php } ?>
  </ul>
<?php } ?>
