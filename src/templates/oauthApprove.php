<?php if($this->user->isOwner()) { ?>
  <h1>Grant <em><?php echo $consumer['name']; ?></em>?</h1>
  <p>
    By clicking approve you are granting this application access to your account.
  </p>
  <form method="post">
    <input type="hidden" name="client_key" value="<?php $this->utility->safe($consumer['id']); ?>">
    <button type="submit">Approve</button>
  </form>
<?php } else { ?>
  <h1>You need to be logged in to view this page.</h1>
  <button class="login-click">Login now</button>
<?php } ?>
