<div class="headerbar">
	Upload <!-- function bar for e.g. sharing function -->
</div>

<div class="message upload-complete"><img src="<?php getTheme()->asset('image', 'checkmark-big.gif'); ?>" align="absmiddle">All done! Go have a <a href="<?php Url::photosView(); ?>">look at your photos</a>.</div>
<div class="message upload-progress"><img src="<?php getTheme()->asset('image', 'upload-big.gif'); ?>" align="absmiddle">Currently uploading <span class="completed">0</span> of <span class="total">0</span> photos.</div>
<form class="upload">
  <div class="options">
    <h2>Apply these settings to my photos.</h2>
    <div>
      <label for="license">License</label>
      <select name="license" class="license">
        <?php foreach($licenses as $code => $license) { ?>
          <option value="<?php Utility::safe($code); ?>" <?php if($license['selected']) { ?> selected="selected" <?php } ?>><?php Utility::licenseLong($code); ?></option>
        <?php } ?>
        <option value="_custom_">*Custom*</option>
      </select>
      <span class="custom">&nbsp; -> <input type="text" name="custom" placeholder="Your custom license goes here"></span>
    </div>
    <div>
      <label for="tags">Tags</label><input type="text" name="tags" class="tags" placeholder="Optional comma separated list">
    </div>
    <div>
      <label for="tags">Permission</label>
      <input type="radio" name="permission" value="1" checked="checked"> Public
      <input type="radio" name="permission" value="0"> Private
    </div>
  </div>
  <input type="hidden" name="crumb" value="<?php Utility::safe($crumb); ?>" class="crumb">
  <div id="uploader">
    <div class="insufficient">
      <img src="<?php echo getTheme()->asset('image', 'error.png'); ?>">
      <h1>Unfortunately, it doesn't look like we support your browser. :(</h1>
      <p>
        Try using <a href="http://www.mozilla.org/en-US/firefox/new/">Firefox</a>.
      </p>
    </div>
  </div>
</form>
<em class="poweredby">Powered by <a href="http://www.plupload.com">Plupload</a>.</em>
