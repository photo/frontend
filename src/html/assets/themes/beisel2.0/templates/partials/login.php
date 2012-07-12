		<div class="modal hide fade" id="loginBox">
			<div class="modal-header">
				<a class="close" data-dismiss="modal">×</a>
				<h3>Sign in to OpenPhoto</h3>
			</div>
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span6">
						<strong>Sign in using Mozilla Persona:</strong><br />
            <img src="<?php $this->theme->asset('image', 'browserid-login.png'); ?>" class="login-click browserid pointer"/>
					</div>
          <?php if($this->plugin->isActive('FacebookConnect') || $this->plugin->isActive('FacebookConnectHosted')) { ?>
            <div class="span6">
              <strong>Sign in using Facebook:</strong><br />
              <img src="<?php $this->theme->asset('image', 'facebook-login.png'); ?>" class="login-click facebook pointer"/>
            </div>
          <?php } ?>
				</div>
        <?php if($this->config->site->allowOpenPhotoLogin == 1) { ?>
          <hr>
          <div class="row">
            <strong>Sign in with your email and password</strong>
            <br>
            <em>This only applies to the owner of this site</em>
            <form class="login-openphoto-submit">
              <fieldset class="control-group">
                <label>Email</label>
                <input type="text" name="email" id="login-email">
              </fieldset>
              
              <fieldset class="control-group">
                <label>Password</label>
                <input type="password" name="password">
              </fieldset>
        
              <button class="btn btn-primary">Login</button> or <a href="#" class="manage-password-request-click">enter your email and click to reset</a>
            </form>
          </div>
        <?php } ?>
			</div>
		</div>
