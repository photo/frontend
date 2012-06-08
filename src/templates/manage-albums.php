<div class="manage albums">

  <?php echo $navigation; ?>
  
  <div class="row hero-unit blurb">
    <h2>What are albums?</h2>
    <p>
      Albums are a collection of photos. You can use them to share photos from a vacation or a child's birthday party.
      <br>
      They're similar to tags but have a few key differences.
      <ol>
        <li><strong class="label label-important">IMPORTANT</strong> Each album is public or private. Public albums and the photos in them are visible to anyone regardless of the privacy setting on the photo itself.</li>
        <li>Private albums can be made visible to your groups.</li>
        <li>Albums are fixed unless you explicitly add a photo to it.</li>
      </ol>
    </p>
  </div>

  <?php echo $albumAddForm; ?>
  
  <?php foreach($albums as $album) { ?>
    <form class="well album-post-submit" action="/album/<?php $this->utility->safe($album['id']); ?>/update">
      <h3>Edit <?php $this->utility->safe($album['name']); ?></h3>
      <label>Name</label>
      <input type="text" name="name" value="<?php $this->utility->safe($album['name']); ?>">
      <div class="control-group">
        <label class="control-label">Permission</label>
        <div class="controls">
          <label class="radio inline">
            <input type="radio" name="permission" id="public" value="1" <?php if($album['permission'] == 1) { ?> checked="checked" <?php } ?>>
            Public
          </label>
          <label class="radio inline">
            <input type="radio" name="permission" id="private" value="0" <?php if($album['permission'] == 0) { ?> checked="checked" <?php } ?>>
            Private
          </label>
        </div>
      </div>
      <?php if(count($groups) > 0) { ?>
        <div class="control-group">
          <label class="control-label">Groups</label>
          <select data-placeholder="Select groups for these photos" multiple  name="groups" class="typeahead">
            <?php foreach($groups as $group) { ?>
              <option value="<?php $this->utility->safe($group['id']); ?>" <?php if(isset($album['groups']) && in_array($group['id'], $album['groups'])) { ?> selected="selected" <?php } ?>><?php $this->utility->safe($group['name']); ?></option>
            <?php } ?>
          </select>
        </div>
      <?php } ?>
      
      <button class="btn">Save</button>&nbsp;&nbsp;&nbsp;<a class="album-delete-click" href="/album/<?php $this->utility->safe($album['id']); ?>/delete">Or delete</a>
    </form>
  <?php } ?>
</div>

