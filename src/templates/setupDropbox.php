<div id="setup">
  <div id="setup-dropbox">
    <form action="/setup/dropbox<?php Utility::safe($qs); ?>" method="post">
      <h3>Enter your Dropbox App credentials</h3>
      <label for="dropboxKey">Dropbox Key</label>
      <input type="text" name="dropboxKey" id="dropboxKey" size="50" autocomplete="off" data-validation="required" placeholder="Dropbox consumer key or app key" value="<?php echo $dropboxKey; ?>">

      <label for="dropboxSecret">Dropbox Secret</label>
      <input type="text" name="dropboxSecret" id="dropboxSecret" size="50" autocomplete="off" data-validation="required" placeholder="Dropbox consumer secret or app secret" value="<?php echo $dropboxSecret; ?>">

      <label for="dropboxFolder">Dropbox Folder Name <em>(a name for the folder we save photos to)</em></label>
      <input type="text" name="dropboxFolder" id="dropboxFolder" size="50" autocomplete="off" data-validation="required" placeholder="Dropbox consumer secret or app secret" value="<?php echo $dropboxFolder; ?>">

      <button type="submit">Continue to Dropbox</button>
    </form>
  </div>
</div>
