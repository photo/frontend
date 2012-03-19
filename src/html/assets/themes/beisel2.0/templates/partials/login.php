		<div class="modal hide fade" id="loginBox">
			<div class="modal-header">
				<a class="close" data-dismiss="modal">×</a>
				<h3>Sign in to OpenPhoto</h3>
			</div>
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span6">
						<strong>Sign in using BrowserID:</strong><br />
            <img src="<?php $this->theme->asset('image', 'browserid-login.png'); ?>" class="login-click browserid pointer"/>
					</div>
          <?php if($this->plugin->isActive('FacebookConnect')) { ?>
            <div class="span6">
              <strong>Sign in using Facebook:</strong><br />
              <img src="<?php $this->theme->asset('image', 'facebook-login.png'); ?>" class="login-click facebook pointer"/>
            </div>
          <?php } ?>
				</div>
			</div>
		</div>
