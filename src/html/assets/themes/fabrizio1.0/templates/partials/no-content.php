<?php if(!isset($type)) $type = null; ?>
<div class="row hero-unit blurb middle">
  <?php if($type == '404') { // 404 error ?>
    <strong class="bigtext">:'(</strong><br/>
    <h1>We could not find the page you were looking for.</h1>
    <p>Perhaps you should <a href="javascript:history.back();"><i class="icon-chevron-left icon-large"></i> go back</a> and make sure you clicked the correct link.</p>
    <p>HTTP 404</p>
  <?php } elseif($type == '500') { // 500 error ?>
    <strong class="bigtext">O.o</strong><br/>
    <h1>We could not find the page you were looking for.</h1>
    <p>Perhaps you should <a href="javascript:history.back();"><i class="icon-chevron-left icon-large"></i> go back</a> and try again.</p>
    <p>HTTP 500</p>
  <?php } elseif(!$this->user->isLoggedIn()) { // user is not logged in ?>
    <?php if($type == 'oauth') { // oauth ?>
      <strong class="bigtext">\o/</strong><br/>
      <h1>You need to sign in to create an app.</h1>
      <p>Start by clicking the <a href="#" class="login-modal-click"><i class="icon-signin icon-large"></i> sign in</a> button.</p>
    <?php } elseif($type == '403') { // requires user to be logged in ?>
      <strong class="bigtext"><i class="icon-lock icon-large"></i></strong><br/>
      <h1>You need to be logged in to view this page.</h1>
      <p>Start by clicking the <a href="#" class="login-modal-click"><i class="icon-signin icon-large"></i> sign in</a> button.</p>
      <p>HTTP 403</p>
    <?php } else { // default with message appropriate for non logged in user ?>
      <strong class="bigtext">:-(</strong><br/>
      <h1>Sorry, nothing to see here. You might want to sign in.</h1>
      <p>Start by clicking the <a href="#" class="login-modal-click"><i class="icon-signin icon-large"></i> sign in</a> or <a href="https://openphoto.me"><i class="icon-plus icon-large"></i> register</a> button.</p>
    <?php } ?>
  <?php } else { // user is logged in ?>
    <?php if($this->user->isOwner() && $type == 'upload') { // message to upload photos ?>
      <strong class="bigtext"><i class="icon-upload-alt icon-large"></i></strong><br/>
      <h1>Either you haven't uploaded photos yet or none matched your search.</h1>
      <p>Start now by clicking the <a href="<?php $this->url->photosUpload(); ?>"><i class="icon-upload-alt icon-large"></i> upload button</a>!</p>
    <?php } else if($this->user->isOwner() && $type == 'albums') { // message to create albums ?>
      <strong class="bigtext"><i class="icon-th icon-large"></i></strong><br/>
      <h1>You haven't created an albums on OpenPhoto yet.</h1>
      <p>Start now by clicking the <a href="<?php $this->url->manageAlbums(); ?>"><i class="icon-th icon-large"></i> manage albums button</a>!</p>
    <?php } elseif($type == '403') { // logged in but no access ?>
      <strong class="bigtext">?</strong><br/>
      <h1>The page you are looking for is restricted.</h1>
      <p>You can <a href="https://openphoto.me"><i class="icon-plus icon-large"></i> register</a> for your own account.</p>
      <p>HTTP 403</p>
    <?php } else { // default with message appropriate for owner/visitor ?>
      <strong class="bigtext">:-(</strong><br/>
      <h1>Sorry, nothing to see here.</h1>
      <?php if($this->user->isOwner()) { ?>
        <p>You can <a href="<?php $this->url->photosUpload(); ?>"><i class="icon-upload-alt icon-large"></i> upload photos</a> or <a href="<?php $this->url->manage(); ?>"><i class="icon-upload-alt icon-large"></i> manage your account</a>.</p>
      <?php } else { ?>
        <p>You can <a href="https://openphoto.me"><i class="icon-plus icon-large"></i> register</a> for your own account.</p>
      <?php } ?>
    <?php } ?>
  <?php } ?>
</div>
