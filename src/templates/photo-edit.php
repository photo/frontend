<div class="owner-edit">
  <h3>This photo belongs to you. You can edit it.</h3>
  <div>
    <div class="detail-form">
      <form method="post" action="<?php Url::photoUpdate($photo['id']); ?>">
        <input type="hidden" name="crumb" value="<?php Utility::safe($crumb); ?>">
        <label>Title</label>
        <input type="text" name="title" value="<?php Utility::safe($photo['title']); ?>">

        <label>Description</label>
        <textarea name="description"><?php Utility::safe($photo['description']); ?></textarea>

        <label>Tags</label>
        <input type="text" name="tags" value="<?php Utility::safe(implode(',', $photo['tags'])); ?>">

        <label>Latitude</label>
        <input type="text" name="latitude" value="<?php Utility::safe($photo['latitude']); ?>">

        <label>Longitude</label>
        <input type="text" name="longitude" value="<?php Utility::safe($photo['longitude']); ?>">

        <label>License</label>
        <?php foreach($licenses as $code => $license) { ?>
          <div>
            <input type="radio" name="license" value="<?php Utility::safe($code); ?>" <?php if($license['selected']) { ?> checked="checked" <?php } ?>>
            <?php Utility::licenseLong($code); ?>
          </div>
        <?php } ?>

        <button type="submit">Update photo</button>
      </form>
    </div>
  </div>
  <div class="delete">
    <form method="post" action="<?php Url::photoDelete($photo['id']); ?>">
      <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
      <button type="submit" class="delete photo-delete-click">Delete this photo</button>
    </form>
  </div>
  <br clear="all">
</div>
