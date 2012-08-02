<div class="manage albums">

  <div class="row hero-unit blurb">
    <h2>What are albums?</h2>
    <p>
      Albums are a collection of photos. You can use them to share photos from a vacation or a child's birthday party.
      <br>
      They're similar to tags but have a few key differences.
      <ol>
        <li>The permission for who can view a photo applies even when it's in an album. If your photo is private then only you'll be able to see them in your album.</li>
        <li>You can specify if an album shows up on the <em>Albums</em> page.</li>
        <li>Albums are fixed unless you explicitly add a photo to it.</li>
        <li>Add photos to an album using the edit form or on the <a href="<?php $this->url->managePhotos(); ?>">manage photos</a> page.</li>
      </ol>
    </p>
  </div>

  <?php echo $albumAddForm; ?>
  
  <?php foreach($albums as $album) { ?>
    <a name="album-<?php $this->utility->safe($album['id']); ?>"></a>
    <form class="well album-post-submit" action="/album/<?php $this->utility->safe($album['id']); ?>/update">
      <h3>
        Edit <?php $this->utility->safe($album['name']); ?>
        <small>
          (
            <?php if($album['count'] > 0) { ?>
              <a href="<?php $this->url->managePhotos(); ?>?album=<?php $this->utility->safe($album['id']); ?>"><?php $this->utility->safe($album['count']); ?> photos</a>
            <?php } else{ ?>
              No photos in this album
            <?php } ?>
          )
        </small>
      </h3>
      <label>Name</label>
      <input type="text" name="name" value="<?php $this->utility->safe($album['name']); ?>">

      <div class="control-group">
        <label class="control-label">Include on Albums page</label>
        <div class="controls">
          <label class="radio inline">
            <input type="radio" name="visible" value="1" <?php if($album['visible'] == 1 || $album['visible'] == '') { ?> checked="checked" <?php } ?>>
            Public
          </label>
          <label class="radio inline">
            <input type="radio" name="visible" value="0" <?php if($album['visible'] == 0) { ?> checked="checked" <?php } ?>>
            Private
          </label>
        </div>
      </div>
      
      <br>
      <button class="btn"><i class="icon-save icon-large"></i> Save</button>&nbsp;&nbsp;&nbsp;<a class="album-delete-click" href="/album/<?php $this->utility->safe($album['id']); ?>/delete">Or delete</a>
    </form>
  <?php } ?>
</div>

