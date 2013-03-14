<div class="manage password">
  <form class="well" method="post">
    <h2>So you forgot your password, huh?</h2>
    <p>
      No problem. Fill out the form below to reset it.
    </p>
    <label>New password</label>
    <input type="password" name="password" class="input-password">

    <label>Confirm new password</label>
    <input type="password" name="password-confirm" class="input-password-confirm">

    <br>
    <button type="button" class="btn btn-primary manage-password-reset-click">Update my password</button>
    
    <input type="hidden" name="token" value="<?php $this->utility->safe($passwordToken); ?>">
  </form>
</div>
