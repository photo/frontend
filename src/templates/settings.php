<h1>Reconfigure Settings</h1>
<form action="/setup">
  <button type="submit">Start Now</button>
  <input type="hidden" name="edit">
</form>

<hr>

<h1>Mobile Passphrase</h1>
<form method="post" action="/user/mobile/passphrase">
  <?php if(!empty($mobilePassphrase)) { ?>
    <div>Your mobile passphrase is: <strong><?php $this->utility->safe($mobilePassphrase['phrase']); ?></strong></div>
    <div><em>Expires in <?php $this->utility->safe($mobilePassphrase['minutes']); ?> minutes.</em></div>
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
    <li class="<?php $this->utility->safe($plugin['status']); ?>">
        <?php $this->utility->safe($plugin['name']); ?> 
        (
          <?php if($plugin['status'] == 'active') { ?>
            <a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/deactivate" class="plugin-status-click">deactivate</a>
          <?php } else { ?>
            <a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/activate" class="plugin-status-click">activate</a>
          <?php } ?>
        )
        <?php if(!empty($plugin['conf']) && $plugin['status'] == 'active') { ?>
          <form method="post" action="/plugin/<?php $this->utility->safe($plugin['name']); ?>/update" method="post">
            <?php foreach($plugin['conf'] as $confName => $confVal) { ?>
              <label><?php $this->utility->safe($confName); ?></label>
              <input type="text" value="<?php $this->utility->safe($confVal); ?>" name="<?php $this->utility->safe($confName); ?>">
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
        <?php $this->utility->safe($credential['name']); ?> (<a href="/oauth/<?php echo $credential['id']; ?>/delete" class="credential-delete-click">delete</a>)
        <ul>
          <li>Consumer key: <?php $this->utility->safe($credential['id']); ?></li>
          <li>Consumer secret: <?php $this->utility->safe($credential['clientSecret']); ?></li>
          <li>OAuth token: <?php $this->utility->safe($credential['userToken']); ?></li>
          <li>OAuth token secret: <?php $this->utility->safe($credential['userSecret']); ?></li>
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
        <?php $this->utility->safe($webhook['callback']); ?> (<a href="/webhook/<?php $this->utility->safe($webhook['id']); ?>/delete" class="webhook-delete-click">delete</a>)
        <ul>
          <li>Topic: <?php $this->utility->safe($webhook['topic']); ?></li>
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
  <div class="clearfix">
    <label>Name</label>
    <div class="input">
      <input type="text" placeholder="Name of new group" value="" name="name">
    </div>
  </div>

  <div class="clearfix">
    <label>Members</label>
    <div class="input">
      <input type="text" placeholder="Email addresses of members (separate with commas)" value="" name="members">
    </div>
  </div>

  <div class="actions">
    <button type="submit" class="group-update-click">Create a new group</button>
  </div>
</form>

<hr>

<?php if(is_array($groups) && count($groups) > 0) { ?>
  <ul class="groups">
    <?php foreach($groups as $group) { ?>
      <li>
        <form method="post" action="/group/<?php $this->utility->safe($group['id']); ?>/update" method="post">
          <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
          <div class="clearfix">
            <label>Name</label>
            <div class="input">
              <input type="text" value="<?php $this->utility->safe($group['name']); ?>" name="name">
            </div>
          </div>

          <div class="clearfix">
            <label>Members</label>
            <div class="input">
              <input type="text" value="<?php $this->utility->safe(implode(',', $group['members'])); ?>" name="members">
            </div>
          </div>

          <div class="actions">
            <button type="submit" class="group-update-click">Update</button>
          </div>
        </form><!--(<a href="/group/<?php echo $group['id']; ?>/delete" class="group-delete-click">delete</a>)-->
      </li>
    <?php } ?>
  </ul>
<?php } else { ?>
  <p>You have no Groups.</p>
<?php } ?>
