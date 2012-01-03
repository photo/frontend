<div class="message upload-complete"><img src="/assets/images/checkmark-big.gif" align="absmiddle">All done! Go have a <a href="<?php $this->url->photosView(); ?>">look at your photos</a>.</div>
<div class="message upload-progress"><img src="/assets/images/upload-big.gif" align="absmiddle">Currently uploading <span class="completed">0</span> of <span class="total">0</span> photos.</div>
<form class="upload">
  <h2>Apply these settings to my photos.</h2>
  <div class="clearfix">
    <label for="license">License</label>
    <div class="input">
      <select name="license" class="license">
        <?php foreach($licenses as $code => $license) { ?>
          <option value="<?php $this->utility->safe($code); ?>" <?php if($license['selected']) { ?> selected="selected" <?php } ?>><?php $this->utility->licenseLong($code); ?></option>
        <?php } ?>
        <option value="_custom_">*Custom*</option>
      </select>
      <span class="custom">&nbsp; -> <input type="text" name="custom" placeholder="Your custom license goes here"></span>
    </div>
  </div>
  <div class="clearfix">
    <label for="tags">Tags</label>
    <div class="input">
      <input type="text" name="tags" class="tags tags-autocomplete" placeholder="Optional comma separated list">
    </div>
  </div>
  <div class="clearfix">
    <label for="tags">Permission</label>
    <div class="input">
      <ul class="inputs-list">
        <li>
          <label>
            <input type="radio" name="permission" value="1" checked="checked">
            <span>Public</span>
          </label>
        </li>
        <li>
          <label>
            <input type="radio" name="permission" value="0">
            <span>Private</span>
          </label>
        </li>
    </div>
  </div>
  <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>" class="crumb">
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
