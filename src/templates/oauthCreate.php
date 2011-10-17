<?php if(User::isOwner()) { ?>
  <form method="post">
    <label>App Name</label>
    <input type="text" name="name" placeholder="Enter a name">
    
    <!--<label>Permission</label>
    <ul>
      <li><input type="checkbox" name="permissions[]" value="read" class="checkbox" checked="true"> Read</li>
      <li><input type="checkbox" name="permissions[]" value="create" class="checkbox"> Create</li>
      <li><input type="checkbox" name="permissions[]" value="update" class="checkbox"> Update</li>
      <li><input type="checkbox" name="permissions[]" value="delete" class="checkbox"> Delete</li>
    </ul>-->

    <input type="hidden" name="oauth_callback" value="<?php Utility::safe($callback); ?>">
    <button type="submit">Create App</button>
  </form>
<?php } else { ?>
  <?php if(Utility::isMobile()) { ?>
    <?php if($error) { ?><h2 class="error">Incorrect Passphrase</h2><?php } ?>
    <form method="post" action="/user/login/mobile">
      <input type="text" name="passphrase" placeholder="Enter your passphrase">
      <input type="hidden" name="redirect" value="<?php Utility::safe($redirect); ?>">
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
