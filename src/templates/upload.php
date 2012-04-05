<div class="message upload-complete"><img src="/assets/images/checkmark-big.gif" align="absmiddle">All done! Go have a <a href="<?php $this->url->photosView('sortBy-dateUploaded,desc'); ?>">look at your photos</a>.</div>
<div class="message upload-warning"><img src="/assets/images/warning-big.png" align="absmiddle">Uh oh! <span class="failed"></span> of <span class="total"></span> photos failed to upload.</div>
<div class="message upload-progress"><img src="/assets/images/upload-big.gif" align="absmiddle">Currently uploading <span class="completed">0</span> of <span class="total">0</span> photos.</div>
<div class="upload-share row">
  <?php $this->plugin->invoke('renderPhotoUploaded', null); ?>
</div>

<form class="upload form-stacked">
  <div class="row">
    <div class="span8">
      <div id="uploader">
        <div class="insufficient">
          <img src="<?php echo getTheme()->asset('image', 'error.png'); ?>">
          <h1>Unfortunately, it doesn't look like we support your browser. :(</h1>
          <p>
            Try using <a href="http://www.mozilla.org/en-US/firefox/new/">Firefox</a>.
          </p>
        </div>
      </div>
      <em class="poweredby">Powered by <a href="http://www.plupload.com">Plupload</a>.</em>
    </div>
    <div class="span4">
      <h2>Use these settings.</h2>
      <label for="tags">Tags</label>
      <input type="text" name="tags" class="typeahead-tags tags tags-autocomplete" placeholder="Optional comma separated list">

      <?php if(count($groups) > 0) { ?>
        <div class="control-group">
          <label class="control-label">Groups</label>
          <?php foreach($groups as $group) { ?>
            <label class="checkbox inline">
              <input type="checkbox" name="groups[]" value="<?php $this->utility->safe($group['id']); ?>">
              <?php $this->utility->safe($group['name']); ?>
            </label>
          <?php } ?>
        </div>
      <?php } ?>

      <div class="control-group">
        <label for="tags">Permission</label>
        <div class="controls">
          <label class="radio inline">
            <input type="radio" name="permission" value="1" checked="checked">
            <span>Public</span>
          </label>
          <label class="radio inline">
            <input type="radio" name="permission" value="0">
            <span>Private</span>
          </label>
        </div>
      </div>

      <label for="license">License</label>
      <select name="license" class="license">
        <?php foreach($licenses as $code => $license) { ?>
          <option value="<?php $this->utility->safe($code); ?>" <?php if($license['selected']) { ?> selected="selected" <?php } ?>><?php $this->utility->licenseName($code); ?></option>
        <?php } ?>
      </select>

      <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>" class="crumb">
      
      <div class="btn-toolbar">
        <button type="button" class="btn btn-primary upload-start-click upload-button"><i class="icon-upload icon-large"></i> Start uploading</button>
      </div>
    </div>
  </div>
</form>
