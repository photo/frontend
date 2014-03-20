<div id="setup">
  <h1>Connect your Dropbox to Trovebox</h1>
  <p>
    <!--If you haven't already created a Dropbox app then <a href="https://www.dropbox.com/developers/apps" target="_blank">click here</a>.-->
    <a href="https://www.dropbox.com/developers/apps" target="_blank">Click here</a> to create a Dropbox app if you haven't already.
    <em>IMPORTANT:</em> make sure you select <em>App Folder</em> access level.
  </p>
  <div id="setup-dropbox">
    <form action="/setup/dropbox<?php $this->utility->safe($qs); ?>" method="post" class="validate">
      <h2>Enter your Dropbox App credentials</h2>
      <label for="dropboxKey">Dropbox Key <em>(<a href="https://www.dropbox.com/developers/apps" target="_blank">found under options</a>)</em></label>
      <input type="password" name="dropboxKey" id="dropboxKey" size="50" autocomplete="off" data-validation="required" placeholder="Dropbox consumer key or app key" value="<?php echo $dropboxKey; ?>">

      <label for="dropboxSecret">Dropbox Secret</label>
      <input type="password" name="dropboxSecret" id="dropboxSecret" size="50" autocomplete="off" data-validation="required" placeholder="Dropbox consumer secret or app secret" value="<?php echo $dropboxSecret; ?>">

      <label for="dropboxFolder">Dropbox Folder Name <em>(a name for the folder we save photos to)</em></label>
      <input type="text" name="dropboxFolder" id="dropboxFolder" size="50" autocomplete="off" data-validation="required alphanumeric" placeholder="An alpha numeric folder name" value="<?php echo $dropboxFolder; ?>">

      <div class="btn-toolbar">
        <?php if(isset($_GET['edit'])) { ?><a class="btn" href="/">Cancel</a><?php } ?>
        <button type="submit" class="btn btn-primary">Continue to Dropbox</button>
      </div>
    </form>
  </div>
</div>
