<div class="owner-edit">
	<h2>This photo belongs to you. You can edit it.</h2>
	<div class="delete">
	  <form method="post" action="<?php $this->url->photoDelete($photo['id']); ?>">
	    <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
	    <button type="submit" class="delete photo-delete-click">Delete this photo</button>
	  </form>
	</div>
  <div class="detail-form">
    <form method="post" action="<?php $this->url->photoUpdate($photo['id']); ?>">
      <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
      <div class="clearfix">
        <label for="title">Title</label>
        <div class="input">
          <input type="text" name="title" id="title" placeholder="A title to describe your photo" value="<?php $this->utility->safe($photo['title']); ?>">
        </div>
      </div>
      <div class="clearfix">
        <label for="description">Description</label>
        <div class="input">
          <textarea name="description" id="description" placeholder="A description of the photo (typically longer than the title)"><?php $this->utility->safe($photo['description']); ?></textarea>
        </div>
      </div>
      <div class="clearfix">
        <label for="tags">Tags</label>
        <div class="input">
          <input type="text" name="tags" id="tags" class="tags-autocomplete" placeholder="A comma separated list of tags" value="<?php $this->utility->safe(implode(',', $photo['tags'])); ?>">
        </div>
      </div>
      <div class="clearfix">
        <label for="latitude">Latitude</label>
        <div class="input">
          <input type="text" name="latitude" id="latitude" placeholder="A latitude value for the location of this photo (i.e. 49.7364565)" value="<?php $this->utility->safe($photo['latitude']); ?>">
        </div>
      </div>
      <div class="clearfix">
        <label for="longtitude">Longitude</label>
        <div class="input">
          <input type="text" name="longitude" id="longtitude" placeholder="A longitude value for the location of this photo (i.e. 181.34523224)" value="<?php $this->utility->safe($photo['longitude']); ?>">
        </div>
      </div>
      <div class="clearfix">
        <label>Permission</label>
        <div class="input">
          <ul class="inputs-list">
            <li>
              <label>
                <input type="radio" name="permission" id="public" value="1" <?php if($photo['permission'] == 1) { ?> checked="checked" <?php } ?>>
                Public
              </label>
            </li>
            <li>
              <label>
                <input type="radio" name="permission" id="private" value="0" <?php if($photo['permission'] == 0) { ?> checked="checked" <?php } ?>>
                Private
              </label>
            </li>
          </ul>
        </div>
      </div>
      <?php if(count($groups) > 0) { ?>
        <div class="clearfix">
          <label>Groups</label>
          <div class="input">
            <ul class="inputs-list">
              <li>
                <label>
                  <input type="checkbox" id="group-none" name="groups[]" value="" <?php if(empty($photo['groups'])) { ?> checked="checked" <?php } ?> class="group-checkbox-click group-checkbox none"> None
                </label>
              </li>
              <?php foreach($groups as $group) { ?>
                <li>
                  <label>
                    <input type="checkbox" name="groups[]" value="<?php $this->utility->safe($group['id']); ?>" <?php if(isset($photo['groups']) && in_array($group['id'], $photo['groups'])) { ?> checked="checked" <?php } ?> class="group-checkbox-click group-checkbox">
                    <?php $this->utility->licenseLong($group['name']); ?>
                  </label>
                </li>
              <?php } ?>
            </ul>
          </div>
        </div>
      <?php } ?>
      <div class="clearfix">
        <label>License</label>
        <div class="input">
          <ul class="inputs-list">
            <?php foreach($licenses as $code => $license) { ?>
            <li>
              <label>
                <input type="radio" name="license" value="<?php $this->utility->safe($code); ?>" <?php if($license['selected']) { ?> checked="checked" <?php } ?>>
                <?php $this->utility->licenseLong($code); ?>
              </label>
            </li>
            <?php } ?>
          </ul>
        </div>
      </div>
      <div class="actions">
        <button type="submit">Update photo</button>
      </div>
    </form>
  </div>
</div>
