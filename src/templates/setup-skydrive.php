<div id="setup">
  <h1>Connect your SkyDrive to Trovebox</h1>
  <p>
    <a href="https://manage.dev.live.com/Applications/Index" target="_blank">Click here</a> to create a Windows live app if you haven't already.
  </p>
  <div id="setup-skydrive">
    <form action="/setup/skydrive<?php $this->utility->safe($qs); ?>" method="post" class="validate">
    
      <h2>Enter your SkyDrive App credentials</h2>
      <label for="SkyDriveClientID">SkyDrive Client Id <em>(<a href="https://manage.dev.live.com/Applications/Index" target="_blank">found under My apps</a>)</em></label>
      <input type="password" name="SkyDriveClientID" id="SkyDriveClientID" size="50" autocomplete="off" data-validation="required" placeholder="SkyDrive Client Id" value="<?php echo $skyDriveClientID; ?>">

      <label for="SkyDriveSecret">SkyDrive Client Secret</label>
      <input type="password" name="SkyDriveClientSecret" id="SkyDriveClientSecret" size="50" autocomplete="off" data-validation="required" placeholder="SkyDrive Client Secret" value="<?php echo $skyDriveClientSecret; ?>">
    
      <h2>Sign Into your SkyDrive Account</h2>
      
      <div class="btn-toolbar">
        <?php if(isset($_GET['edit'])) { ?><a class="btn" href="/">Cancel</a><?php } ?>
        <button type="submit" class="btn btn-primary">Continue</button>
      </div>
    </form>
  </div>
</div>
