<div class="account">
  <?php echo $navigation; ?>

  <h2>Your Account</h2>

  <div class="row hero-unit blurb">
    <br>
    <p>
      Soon your account page will allow you to update your email address, change storage providers and more.
      For now, it's just an overview.
    </p>
    <h3>Your email address</h3>
    <div class="inset">
      <?php echo $email; ?>
    </div>
    <br>
    <h3>Where are your files stored?</h3>
    <div class="inset">
      <?php if($systems['FileSystem'] === 'S3') { ?>
        <?php if($aws['bucket'] === 'awesomeness.openphoto.me') { ?>
          Storage provided by openphoto.me
        <?php } else { ?>     
          In your S3 bucket (<?php $this->utility->safe($aws['bucket']); ?>)
        <?php } ?>
      <?php } elseif($systems['FileSystem'] === 'S3Dropbox') { ?>
        Your Dropbox account
      <?php } ?>
      <div>
        <em>You'll be able to change this soon</em>
      </div>
    </div>
  </div>
</div>
