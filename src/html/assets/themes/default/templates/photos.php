  <?php if($photos[0]['totalRows'] > 0) { ?>
    <?php getTheme()->display('partials/pagination.php', $pagination); ?>
    <ul class="photo-grid grid-200">
      <?php foreach($photos as $photo) { ?>
        <li class="grid-item id-<?php Utility::safe($photo['id']); ?>">
          <a href="/photo/<?php Utility::safe($photo['id']); ?>"><img src="<?php Utility::photoUrl($photo, getConfig()->get('photo')->thumbnailSize); ?>" alt="<?php Utility::safe($photo['title']); ?>"></a>
          <ul class="meta">
            <li class="age"><?php Utility::timeAsText($photo['dateTaken'], 'Taken'); ?></li>
            <li class="permission <?php Utility::permissionAsText($photo['permission']); ?>"><?php Utility::permissionAsText($photo['permission']); ?></li>
            <?php if(!empty($photo['tags'])) { ?><li><img src="/assets/img/default/icon-tags.png" alt="This photo has tags"></li><?php } ?>
            <?php if(!empty($photo['latitude'])) { ?><li><img src="/assets/img/default/icon-globe.png" alt="This photo has location information"></li><?php } ?>
          </ul>
        </li>
      <?php } ?>
    </ul>
    <br clear="all">
    <?php getTheme()->display('partials/pagination.php', array_merge($pagination, array('labelPosition' => 'bottom'))); ?>
  <?php } else { ?>
    <?php /* TODO: more intelligent message */ ?>
    <h2>You haven't uploaded any photos yet</h2>
  <?php } ?>
