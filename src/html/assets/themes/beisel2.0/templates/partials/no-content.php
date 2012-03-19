<div class="row hero-unit empty middle">
  <?php if(!$this->user->isLoggedIn()) { // user is not logged in ?>
    <strong class="bigtext">:-(</strong><br/>
    <h1>Sorry, nothing to see here. You might want to sign in.</h1>
    <p>Start by click the <a href="#" class="login-modal-click"><i class="icon-signin icon-large"></i> sign in</a> button.</p>
  <?php } else { // user is logged in ?>
    <?php if($type == 'upload') { // message to upload folders ?>
      <br/>
      <strong class="bigtext"><i class="icon-upload icon-large"></i></strong><br/>
      <h1>You haven't uploaded any photos to OpenPhoto yet.</h1>
      <p>Start now by clicking the <a href="<?php $this->url->photosUpload(); ?>"><i class="icon-upload icon-large"></i> upload button</a>!</p>
    <?php } else { // default with message appropriate for owner/visitor ?>
      <strong class="bigtext">:-(</strong><br/>
      <h1>Sorry, nothing to see here.</h1>
      <?php if($this->user->isOwner()) { ?>
        <p>You can <a href="<?php $this->url->photosUpload(); ?>"><i class="icon-upload icon-large"></i> upload photos</a> or <a href="<?php $this->url->manage(); ?>"><i class="icon-upload icon-large"></i> manage your account</a>.</p>
      <?php } else { ?>
        <p>You can <a href="https://openphoto.me"><i class="icon-plus icon-large"></i> register</a> for your own account.</p>
      <?php } ?>
    <?php } ?>
  <?php } ?>
</div>
