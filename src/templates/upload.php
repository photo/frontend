<div class="hide row hero-unit blurb upload-confirm"></div>
<div class="row hero-unit upload-container">
  <div class="upload-message upload-progress"><img src="/assets/images/upload-big.gif" align="absmiddle">Currently uploading <span class="completed">0</span> of <span class="total">0</span> photos.</div>

  <form class="upload form-stacked photo-upload-submit" method="post" action="/photo/upload">
    <div class="row">
      <div class="span8">
        <div id="uploader">
          <div class="insufficient">
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
        <!--<select class="typeahead-tags tags tags-autocomplete"  data-placeholder="Select tags for these photos" multiple  name="tags"></select>-->
        <input type="text" name="tags" class="tags" placeholder="Optional comma separated list">

        <div class="control-group">
          <label class="control-label">Albums <em>(<a href="/album/form" class="album-form-click">create a new one</a>)</em></label>
          <select data-placeholder="Select albums for these photos" multiple  name="albums" class="typeahead">
            <?php if(!empty($albums)) { ?>
              <?php foreach($albums as $album) { ?>
                <option value="<?php $this->utility->safe($album['id']); ?>"><?php $this->utility->safe($album['name']); ?></option>
              <?php } ?>
            <?php } ?>
          </select>
        </div>

        <div class="control-group">
          <label class="control-label">Groups <em>(<a href="/group/form" class="group-form-click" target="_blank">create a new one</a>)</em></label>
          <select data-placeholder="Select groups for these photos" multiple  name="groups" class="typeahead">
            <?php if(!empty($groups)) { ?>
              <?php foreach($groups as $group) { ?>
                <option value="<?php $this->utility->safe($group['id']); ?>"><?php $this->utility->safe($group['name']); ?></option>
              <?php } ?>
            <?php } ?>
          </select>
        </div>

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
          <button type="submit" class="btn btn-primary upload-button"><i class="icon-upload-alt icon-large"></i> Start uploading</button>
        </div>
      </div>
    </div>
  </form>
</div>
