<?php if($this->user->isOwner()) { ?>
  <div class="row">
    <div class="span8 offset3">
      <form method="post" class="form-stacked">
        <h1>Grant <em><?php echo $consumer['name']; ?></em>?</h1>
        <p>
          By clicking approve you grant this application access to your account.
        </p>
        <input type="hidden" name="client_key" value="<?php $this->utility->safe($consumer['id']); ?>">
        <button type="submit">Approve</button>
      </form>
    </div>
  </div>
<?php } else { ?>
  <h1>You need to be logged in to view this page.</h1>
  <button class="login-click">Login now</button>
<?php } ?>
