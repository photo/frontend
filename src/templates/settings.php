<h1>Reconfigure settings</h1><br>
<div>
  <a href="/setup?edit" class="button">Start Now</a>
</div>

<hr>

<h1>OAuth Credentials</h1>
<?php if(is_array($credentials) && count($credentials) > 0) { ?>
  <ul class="credentials">
    <?php foreach($credentials as $credential) { ?>
      <li>
        <?php Utility::safe($credential['name']); ?> (<a href="/oauth/<?php echo $credential['id']; ?>/delete" class="credential-delete-click">delete</a>)
        <ul>
          <li>Consumer key: <?php Utility::safe($credential['id']); ?></li>
          <li>Consumer secret: <?php Utility::safe($credential['clientSecret']); ?></li>
          <li>OAuth token: <?php Utility::safe($credential['userToken']); ?></li>
          <li>OAuth token secret: <?php Utility::safe($credential['userSecret']); ?></li>
        </ul>
      </li>
    <?php } ?>
  </ul>
<?php } else { ?>
  <p>You have no OAuth credentials.</p>
<?php } ?>

<hr>

<h1>Groups</h1>
<form method="post" action="/group/create" method="post">
  <label>Name</label>
  <input type="text" placeholder="Name of new group" value="" name="name">

  <label>Members</label>
  <input type="text" placeholder="Email addresses of members (separate with commas)" value="" name="members">
  
  <button type="submit" class="group-update-click">Create a new group</button>
</form>

<hr>

<?php if(is_array($groups) && count($groups) > 0) { ?>
  <ul class="groups">
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
