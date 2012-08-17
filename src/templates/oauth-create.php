<?php if($this->user->isOwner()) { ?>
  <div class="manage applications">
    <div class="row hero-unit blurb">
      <h2>Create an app</h2>
      <p>
        <h3>Make sure you trust the site you just came from.</h3>
        If you do not, then simply close this window.
        Apps let other programs have access to your OpenPhoto account. 
        You'll want to make sure that you trust the site you just came from.
      </p>
    </div>

    <form method="post" class="well">
      <label for="name">A Name For Your App</label>
      <input type="text" name="name" placeholder="Enter a name" value="<?php $this->utility->safe($name); ?>">

      <!--<label>Permission</label>
      <ul>
        <li><input type="checkbox" name="permissions[]" value="read" class="checkbox" checked="true"> Read</li>
        <li><input type="checkbox" name="permissions[]" value="create" class="checkbox"> Create</li>
        <li><input type="checkbox" name="permissions[]" value="update" class="checkbox"> Update</li>
        <li><input type="checkbox" name="permissions[]" value="delete" class="checkbox"> Delete</li>
      </ul>-->

      <div>
        <button type="submit" class="btn btn-primary"><i class="icon-save icon-large"></i> Create and Approve</button>
      </div>
      <input type="hidden" name="oauth_callback" value="<?php $this->utility->safe($callback); ?>">
    </form>
  </div>
<?php } else { ?>
  <?php $this->theme->display('partials/no-content.php', array('type' => 'oauth')); ?>
<?php } ?>
