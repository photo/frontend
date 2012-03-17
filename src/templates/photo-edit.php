<form method="post" action="<?php $this->url->photoUpdate($photo['id']); ?>" id="photo-edit-form">
  <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
  <label for="title">Title</label>
  <input type="text" name="title" id="title" placeholder="A title to describe your photo" value="<?php $this->utility->safe($photo['title']); ?>">

  <label for="description">Description</label>
  <textarea name="description" id="description" placeholder="A description of the photo (typically longer than the title)"><?php $this->utility->safe($photo['description']); ?></textarea>

  <label for="tags">Tags</label>
  <input type="text" name="tags" id="tags" class="tags-autocomplete" placeholder="A comma separated list of tags" value="<?php $this->utility->safe(implode(',', $photo['tags'])); ?>">

  <label for="latitude">Latitude &amp; Longitude</label>
  <input type="text" class="input-small" name="latitude" id="latitude" placeholder="Lat, i.e. 49.73" value="<?php $this->utility->safe($photo['latitude']); ?>">
  <input type="text" class="input-small" name="longitude" id="longtitude" placeholder="Lon, i.e. 18.34" value="<?php $this->utility->safe($photo['longitude']); ?>">

  <div class="control-group">
    <label class="control-label">Permission</label>
    <div class="controls">
      <label class="radio inline">
        <input type="radio" name="permission" id="public" value="1" <?php if($photo['permission'] == 1) { ?> checked="checked" <?php } ?>>
        Public
      </label>
      <label class="radio inline">
        <input type="radio" name="permission" id="private" value="0" <?php if($photo['permission'] == 0) { ?> checked="checked" <?php } ?>>
        Private
      </label>
    </div>
  </div>
  <?php if(count($groups) > 0) { ?>
    <div class="control-group">
      <label class="control-label">Groups</label>
      <div class="controls">
        <label class="checkbox inline">
          <input type="checkbox" id="group-none" name="groups[]" value="" <?php if(empty($photo['groups'])) { ?> checked="checked" <?php } ?> class="group-checkbox-click group-checkbox none"> None
        </label>
        <?php foreach($groups as $group) { ?>
          <label class="checkbox inline">
            <input type="checkbox" name="groups[]" value="<?php $this->utility->safe($group['id']); ?>" <?php if(isset($photo['groups']) && in_array($group['id'], $photo['groups'])) { ?> checked="checked" <?php } ?> class="group-checkbox-click group-checkbox">
            <?php $this->utility->safe($group['name']); ?>
          </label>
        <?php } ?>
      </div>
    </div>
  <?php } ?>

  <label>License</label>
  <div class="input">
    <select name="license">
      <?php foreach($licenses as $code => $license) { ?>
        <option value="<?php $this->utility->safe($code); ?>"<?php if($license['selected']) { ?> selected="selected" <?php } ?>><?php $this->utility->licenseLong($code); ?></option>
      <?php } ?>
    </select>
  </div>
</form>
