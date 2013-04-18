<form class="upload dropzone form-stacked" method="post" action="/photo/upload">
  <div class="row">
    <div class="span3">
      <h3>Use these settings.</h3>
      <br>
      <label for="tags">Tags</label>
      <input type="search" name="tags" class="typeahead tags" autocomplete="off" placeholder="Optional comma separated list">

      <div class="control-group">
        <label class="control-label">Albums <small>(<a href="#" class="showBatchForm album" data-action="albums">create new</a>)</small></label>
        <select data-placeholder="Select albums for these photos" name="albums" class="typeahead">
          <option value="">If you'd like, choose an album</option>
          <?php foreach($albums as $album) { ?>
            <option value="<?php $this->utility->safe($album['id']); ?>"><?php $this->utility->safe($album['name']); ?></option>
          <?php } ?>
        </select>
      </div>

      <div class="control-group">
        <label for="tags">Permission</label>
        <div class="controls">
          <label class="radio inline private">
            <input type="radio" name="permission" value="0"<?php if($preferences['permission'] === false || $preferences['permission'] === '0') { ?> checked="checked"<?php } ?>>
            <span>Private</span>
          </label>
          <label class="radio inline">
            <input type="radio" name="permission" value="1"<?php if($preferences['permission'] === '1') { ?> checked="checked"<?php } ?>>
            <span>Public</span>
          </label>
        </div>
      </div>

      <label for="license">License</label>
      <select name="license" class="license">
        <?php foreach($licenses as $code => $license) { ?>
          <option value="<?php $this->utility->safe($code); ?>" <?php if($license['selected']) { ?> selected="selected" <?php } ?>><?php $this->utility->licenseName($code); ?></option>
        <?php } ?>
      </select>

      <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
      
      <div class="btn-toolbar">
        <button type="button" class="btn btn-theme-secondary photo-upload">Start uploading</button>
      </div>
      <small><em>Powered by <a href="http://www.dropzonejs.com/">Dropzone.js</a></em></small>
    </div>
    <div class="span9">
      <div class="bucket">
         <div class="default message"></div>
         <div class="preview-container"></div>
      </div>
    </div>
  </div>
</form>
