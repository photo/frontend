<h1>OAuth Credentials</h1>
<?php if(is_array($credentials) && count($credentials) > 0) { ?>
  <ul>
    <?php foreach($credentials as $credential) { ?>
      <li><?php Utility::safe($credential['name']); ?> (<a href="/oauth/<?php echo $credential['id']; ?>/delete" class="credential-delete-click">delete</a>)</li>
    <?php } ?>
  </ul>
<?php } else { ?>
  <p>You have no OAuth credentials.</p>
<?php } ?>

<hr>

<h1>Groups</h1>
<?php if(is_array($groups) && count($groups) > 0) { ?>
  <ul class="credentials">
    <?php foreach($groups as $group) { ?>
      <li>
        <form method="post" action="/group/<?php Utility::safe($group['id']); ?>/update" method="post">
          <label>Name</label>
          <input type="text" value="<?php Utility::safe($group['name']); ?>" name="name">

          <label>Members</label>
          <input type="text" value="<?php Utility::safe(implode(',', $group['members'])); ?>" name="members">
          
          <button type="submit" class="group-update-click">Update</button>
        </form><!--(<a href="/group/<?php echo $group['id']; ?>/delete" class="group-delete-click">delete</a>)-->
      </li>
    <?php } ?>
  </ul>
<?php } else { ?>
  <p>You have no Groups.</p>
<?php } ?>
