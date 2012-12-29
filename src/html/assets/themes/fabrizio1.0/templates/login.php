<div class="row">
  <h3>Sign in to OpenPhoto</h3>
  <div class="span6">
    <strong>Sign in using Mozilla Persona:</strong><br />
    <img src="<?php $this->theme->asset('image', 'browserid-login.png'); ?>" class="login-click browserid pointer"/>
  </div>
  <?php if($this->plugin->isActive('FacebookConnect')) { ?>
    <div class="span6">
      <strong>Sign in using Facebook:</strong><br />
      <img src="<?php $this->theme->asset('image', 'facebook-login.png'); ?>" class="login-click facebook pointer"/>
    </div>
  <?php } ?>
  <?php if($this->config->site->allowOpenPhotoLogin == 1) { ?>
    <hr>
    <div class="row">
      <strong>Sign in with your email and password</strong>
      <br>
      <em>This only applies to the owner of this site</em>
      <form class="login">
        <fieldset class="control-group">
          <label>Email</label>
          <input type="text" name="email" id="login-email">
        </fieldset>
        
        <fieldset class="control-group">
          <label>Password</label>
          <input type="password" name="password">
        </fieldset>
  
        <button class="btn btn-brand">Login</button> or <a href="#" class="manage-password-request-click">enter your email and click to reset</a>
        <input type="hidden" name="r" value="<?php $this->utility->safe($r); ?>">
      </form>
    </div>
  <?php } ?>
</div>
