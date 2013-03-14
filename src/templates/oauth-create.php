<div class="row">
  <div class="span12">
    <h2>Create an app</h2>
  </div>
</div>
<div class="row">
  <div class="span6">
    <p>
      <h3>Make sure you trust the site you just came from.</h3>
      If you do not, then simply close this window.
      Apps let other programs have access to your Trovebox account. 
      You'll want to make sure that you trust the site you just came from.
    </p>
  </div>
</div>
<div class="row">
  <div class="span12">
    <form method="post">
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
        <button type="submit" class="btn btn-primary"><?php if($tokenType === 'access') { ?>Create and Approve<?php } else { ?>Create<?php } ?></button>
      </div>
      <input type="hidden" name="tokenType" value="<?php $this->utility->safe($tokenType); ?>">
      <input type="hidden" name="oauth_callback" value="<?php $this->utility->safe($callback); ?>">
    </form>
  </div>
</div>
