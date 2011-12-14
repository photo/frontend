<h1>Reconfigure Settings</h1>
<form action="/setup">
  <button type="submit">Start Now</button>
  <input type="hidden" name="edit">
</form>

<hr>

<h1>Mobile Passphrase</h1>
<form method="post" action="/user/mobile/passphrase">
  <?php if(!empty($mobilePassphrase)) { ?>
    <div>Your mobile passphrase is: <strong><?php Utility::safe($mobilePassphrase['phrase']); ?></strong></div>
    <div><em>Expires in <?php Utility::safe($mobilePassphrase['minutes']); ?> minutes.</em></div>
    <button type="submit">Reset Passphrase</button>
  <?php } else { ?>
    <button type="submit">Create Passphrase</button>
  <?php } ?>
</form>

<hr>

<h1>Plugins</h1>

<?php if(!empty($plugins)) { ?>
  <ul class="plugins">
    <?php foreach($plugins as $plugin) { ?>
    <li class="<?php Utility::safe($plugin['status']); ?>">
        <?php Utility::safe($plugin['name']); ?> 
        (
          <?php if($plugin['status'] == 'active') { ?>
            <a href="/plugin/<?php Utility::safe($plugin['name']); ?>/deactivate" class="plugin-status-click">deactivate</a>
          <?php } else { ?>
            <a href="/plugin/<?php Utility::safe($plugin['name']); ?>/activate" class="plugin-status-click">activate</a>
          <?php } ?>
        )
        <?php if(!empty($plugin['conf']) && $plugin['status'] == 'active') { ?>
          <form method="post" action="/plugin/<?php Utility::safe($plugin['name']); ?>/update" method="post">
            <?php foreach($plugin['conf'] as $confName => $confVal) { ?>
              <label><?php Utility::safe($confName); ?></label>
              <input type="text" value="<?php Utility::safe($confVal); ?>" name="<?php Utility::safe($confName); ?>">
            <?php } ?>
            <button type="submit" class="plugin-update-click">Update</button>
          </form>
        <?php } ?>
      </li>
    <?php } ?>
  </ul>
<?php } else { ?>
  <p>You have no Plugins</p>
<?php } ?>

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

<h1>Webhooks</h1>
<?php if(is_array($webhooks) && count($webhooks) > 0) { ?>
  <ul class="webhooks">
    <?php foreach($webhooks as $webhook) { ?>
      <li>
        <?php Utility::safe($webhook['callback']); ?> (<a href="/webhook/<?php Utility::safe($webhook['id']); ?>/delete" class="webhook-delete-click">delete</a>)
        <ul>
          <li>Topic: <?php Utility::safe($webhook['topic']); ?></li>
        </ul>
      </li>
    <?php } ?>
  </ul>
<?php } else { ?>
  <p>You have no Webhooks.</p>
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
