<div class="row login">
  <?php if($this->config->site->allowOpenPhotoLogin == 1) { ?>
    <div class="span6">
    <h4>Sign in with your email and password</h4>
    <form class="login">
      <fieldset class="control-group">
        <label>What's your email address?</label>
        <input type="text" name="email" placeholder="user@example.com" id="login-email">
      </fieldset>
      
      <fieldset class="control-group">
        <label>Enter your password below</label>
        <input type="password" name="password" placeholder="Your password">
      </fieldset>

      <button class="btn btn-brand">Login</button> or <a href="#" class="manage-password-request-click">enter your email and click to reset</a>
      <input type="hidden" name="r" value="<?php $this->utility->safe($r); ?>">
    </form>
    </div>
  <?php } ?>
  <div class="span6 alternate">
    <strong>You can also sign in using...</strong>
    <ul class="unstyled">
      <li><a href="" class="btn btn-theme-secondary">Mozilla Persona</a></li>
      <?php if(true || $this->plugin->isActive('FacebookConnect')) { ?>
        <li><a href="" class="btn btn-theme-secondary">Facebook Connect</a></li>
      <?php } ?>
    </ul>
  </div>
</div>
