<?php if(!empty($photos)) { ?>
  <?php getTheme()->display('partials/pagination.php', $pages); ?>
  <ul class="photo-grid grid-200">
    <?php foreach($photos as $photo) { ?>
      <li class="grid-item id-<?php Utility::safe($photo['id']); ?>">
        <a href="<?php Url::photoView($photo['id'], $options); ?>"><img src="<?php Url::photoUrl($photo, getConfig()->get('photoSizes')->thumbnail); ?>" alt="<?php Utility::safe($photo['title']); ?>"></a>
        <ul class="meta">
          <li class="age"><?php Utility::timeAsText($photo['dateTaken'], 'Taken'); ?></li>
          <li class="permission <?php Utility::permissionAsText($photo['permission']); ?>"><?php Utility::permissionAsText($photo['permission']); ?></li>
          <?php if(!empty($photo['tags'])) { ?><li class="tags"><img src="<?php getTheme()->asset('image', 'icon-tags.png'); ?>" alt="This photo has tags"></li><?php } ?>
          <?php if(!empty($photo['latitude'])) { ?><li class="geo"><img src="<?php getTheme()->asset('image', 'icon-globe.png'); ?>" alt="This photo has location information"></li><?php } ?>
        </ul>
      </li>
    <?php } ?>
  </ul>
  <br clear="all">
  <?php getTheme()->display('partials/pagination.php', array_merge($pages, array('labelPosition' => 'bottom'))); ?>
<?php } else { ?>
  <?php if(User::isOwner()) { ?>
    <h1>There don't seem to be any photos. You should <a href="<?php Url::photosUpload(); ?>">upload</a> some.</h1>
    <p>
      If you're searching for photos then there aren't any which match your query.
    </p>
  <?php } else { ?>
    <h1>No photos to show.</h1>
    <p>
      This could be because the user hasn't uploaded any photos yet or you've searched for photos that do not exist.
    </p>
  <?php } ?>
<?php } ?>
