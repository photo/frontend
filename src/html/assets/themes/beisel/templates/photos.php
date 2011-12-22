<?php $thumbnailSize = isset($_GET['size']) ? $_GET['size'] : '280x186xCR'; ?>
<?php if(!empty($photos)) { ?>
  <?php getTheme()->display('partials/pagination.php', $pages); ?>
  <ul class="photo-grid <?php Utility::safe("size{$thumbnailSize}"); ?>">
    <?php foreach($photos as $photo) { ?>
      <li class="grid-item id-<?php Utility::safe($photo['id']); ?> pin-out">
        <a href"#" class="pin pin-click" data-id="<?php Utility::safe($photo['id']); ?>"></a>
        <a href="<?php Url::photoView($photo['id'], $options); ?>">
          <img src="<?php Url::photoUrl($photo, $thumbnailSize); ?>" alt="<?php Utility::safe($photo['title']); ?>" class="thumbnail pin-over" />
          <ul class="meta">
            <li class="age"><?php Utility::timeAsText($photo['dateTaken'], 'Taken'); ?></li>
            <li class="permission"><span class="<?php Utility::permissionAsText($photo['permission']); ?>" title="Photo is <?php Utility::permissionAsText($photo['permission']); ?>"></li>
            <?php if(isset($photo['tags']) && !empty($photo['tags'])) { ?><li class="tags"><span title="Photo has tags"></span></li><?php } ?>
            <?php if(isset($photo['latitude']) && !empty($photo['latitude'])) { ?><li class="geo"><span title="Photo has geo informations"></span></li><?php } ?>
          </ul>
        </a>
      </li>
    <?php } ?>
  </ul>
  <br clear="all">
  <span class="paginationbottom"><?php getTheme()->display('partials/pagination.php', array_merge($pages, array('labelPosition' => 'bottom'))); ?></span>
<?php } else { ?>
  <?php if(User::isOwner()) { ?>
    <a href="<?php Url::photoUpload(); ?>" class="link" title="Start uploading now!"><img src="<?php getTheme()->asset('image', 'front.png'); ?>" class="front" /></a>
    <h1>There don't seem to be any photos. You should <a href="<?php Url::photosUpload(); ?>" class="link">upload</a> some.</h1>
    <p>
      If you're searching for photos then there aren't any which match your query.
    </p>
  <?php } else { ?>
	<img src="<?php getTheme()->asset('image', 'front-general.png'); ?>" class="front" />
    <h1>No photos to show.</h1>
    <p>
      This could be because the user hasn't uploaded any photos yet or you've searched for photos that do not exist.
    </p>
  <?php } ?>
<?php } ?>
