<div class="headerbar">
	Upload <!-- function bar for e.g. sharing function -->
</div>

<div class="upload-complete"><img src="<?php getTheme()->asset('image', 'checkmark-big.gif'); ?>" align="absmiddle">All done! Go have a <a href="<?php Url::photosView(); ?>">look at your photos</a>.</div>
<form class="upload">
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
