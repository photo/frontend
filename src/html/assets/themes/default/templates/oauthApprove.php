<h1>Would you like ot grant <?php echo $consumer['name']; ?> access to your account?</h1>
<p>
  <h2>You are providing the following permissions</h2>
  <ul>
    <?php foreach($consumer['permissions'] as $permission) { ?>
      <li><?php echo $permission; ?></li>
    <?php } ?>
  </ul>
</p>
<form method="post">
  <input type="hidden" name="client_key" value="<?php Utility::safe($consumer['id']); ?>">
  <button type="submit">Approve</button>
</form>
