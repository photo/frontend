<?php if($this->user->isOwner()) { ?>
  <div class="row">
    <div class="span8 offset3">
      <form method="post" class="form-stacked">
        <h1>Create an app</h1>
        <p>
          Apps let other programs have access to your OpenPhoto account. 
          It's a great way to add features and functionality to your photo library.
          You'll want to make sure that you trust the site you just came from.
          If you do not, then simply close this window.
        </p>
        <div class="clearfix">
          <label for="name">A Name For Your App</label>
          <div class="input">
            <input type="text" name="name" placeholder="Enter a name" value="<?php $this->utility->safe($name); ?>">
          </div>
        </div>

        <!--<label>Permission</label>
        <ul>
          <li><input type="checkbox" name="permissions[]" value="read" class="checkbox" checked="true"> Read</li>
          <li><input type="checkbox" name="permissions[]" value="create" class="checkbox"> Create</li>
          <li><input type="checkbox" name="permissions[]" value="update" class="checkbox"> Update</li>
          <li><input type="checkbox" name="permissions[]" value="delete" class="checkbox"> Delete</li>
        </ul>-->

        <input type="hidden" name="oauth_callback" value="<?php $this->utility->safe($callback); ?>">
        <button type="submit">Create App</button>
      </form>
    </div>
  </div>
<?php } else { ?>
  <?php if($this->utility->isMobile()) { ?>
    <?php if($error) { ?><h2 class="error">Incorrect Passphrase</h2><?php } ?>
    <form method="post" action="/user/login/mobile">
      <input type="text" name="passphrase" placeholder="Enter your passphrase">
      <input type="hidden" name="redirect" value="<?php $this->utility->safe($redirect); ?>">
      <button type="submit">Continue</button>
    </form>
    <h1>Don't have one?</h1>
    <ol class="steps">
      <li>Sign in from your computer.</li>
      <li>Click next to your email.</li>
      <li>Go to settings.</li>
      <li>Generate a new passphrase.</li>
    <ol>
  <?php } else { ?>
    <h1>You need to be logged in to view this page.</h1>
    <button class="login-click">Login now</button>
  <?php } ?>
<?php } ?>
