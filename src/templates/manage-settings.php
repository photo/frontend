<div class="row">
  <div class="span12">
    <h1>Configure your Trovebox site</h1>
  </div>
</div>

<a name="settings"></a>
<div class="row">
  <div class="span6">
    Don't want users to download your original photos? No problem. Want to allow the same photo to be uploaded twice? You're at the right spot.
  </div>
</div>
<form method="post" action="/manage/settings">
  <div class="row">
    <div class="span12">
      <h3>General settings</h3>
      <div class="controls">
        <label class="checkbox inline">
          <input type="checkbox" name="enableBetaFeatures" value="1" <?php if($enableBetaFeatures) { ?>checked="checked"<?php } ?>>
          Enable beta features on this site and on the mobile apps
        </label>
      </div>
      <div class="controls">
        <label class="checkbox inline">
          <input type="checkbox" name="allowDuplicate" value="1" <?php if($allowDuplicate) { ?>checked="checked"<?php } ?>>
          The same photo can be uploaded more than once
        </label>
      </div>
      <div class="controls">
        <label class="checkbox inline">
          <input type="checkbox" name="downloadOriginal" value="1" <?php if($downloadOriginal) { ?>checked="checked"<?php } ?>>
          Let visitors download my original hi-res photos
        </label>
      </div>
      <div class="controls">
        <label class="checkbox inline">
          <input type="checkbox" name="hideFromSearchEngines" value="1" <?php if($hideFromSearchEngines) { ?>checked="checked"<?php } ?>>
          Hide my site from search engines
        </label>
      </div>
      <div class="controls">
        <label class="checkbox inline">
          <input type="checkbox" name="decreaseLocationPrecision" value="1" <?php if($decreaseLocationPrecision) { ?>checked="checked"<?php } ?>>
          Decrease the accuracy when displaying my photos on a map for others
        </label>
      </div>
    </div>

    <div class="span6">
      <a name="admins"></a>
      <h5>Collaborators</h5>
      <p>
        Enter email addresses for others you'd like to collaborate with you. These users will have full access to your account. They can log in using Facebook.
      </p>
      <div class="controls">
        <?php for($i=0; $i<4; $i++) { ?>
          <input type="text" name="admins[<?php echo $i; ?>]" <?php if(isset($admins[$i])) { ?> value="<?php $this->utility->safe($admins[$i]); ?>" <?php } ?> placeholder="user<?php echo ($i+1); ?>@example.com">
        <?php } ?>
      </div>
      <div class="btn-toolbar"><button class="btn btn-brand">Save</button></div>
    </div>
  </div>
  <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
</form>

<a name="apps"></a>
<div class="row">
  <div class="span12">
    <h3>Your OAuth Applications</h3>
  </div>
</div>
<div class="row">
  <div class="span6">
    You've granted these applications access to your Trovebox account. Clicking <strong>revoke</strong> cannot be undone and you may have to reapprove the application.
  </div>
</div>
<div class="row">
  <div class="span12">
    <p>
      <a href="/v1/oauth/authorize?oauth_callback=<?php $this->utility->safe(sprintf('%s://%s%s', $this->utility->getProtocol(false), $_SERVER['HTTP_HOST'], '/manage/apps/callback')); ?>&name=<?php $this->utility->safe(urlencode('Self Generated App')); ?>&tokenType=access" class="btn btn-brand">Create a new app</a>
    </p>
    <?php if(!empty($credentials)) { ?>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Application Name</th>
            <th></th>
          </tr>
        </thead>
        <?php foreach($credentials as $credential) { ?>
          <tr>
            <td>
              <?php $this->utility->safe($credential['name']); ?>
              <?php if(!empty($credential['dateCreated'])) { ?>
                <small><em class="credential-date">(<?php $this->utility->safe(ucwords($credential['type'])); ?> token created on <?php $this->utility->dateLong($credential['dateCreated']); ?>)</em></small>
              <?php } ?>
            </td>
            <td>
              <div class="pull-right">
              <a href="/v1/oauth/<?php $this->utility->safe($credential['id']); ?>/markup" class="credentialView"><i class="icon-eye-open icon-large"></i> View</a>
                &nbsp; &nbsp; &nbsp;
                <a href="/oauth/<?php $this->utility->safe($credential['id']); ?>/delete" class="credentialDelete"><i class="icon-trash icon-large"></i> Revoke</a>
              </div>
            </td>
          </tr>
        <?php } ?>
      </table>
    <?php } ?>
  </div>
</div>

<a name="plugins"></a>
<div class="row">
  <div class="span12">
    <h2>Your plugins</h2>
  </div>
</div>
<div class="row">
  <div class="span6">
    Plugins help you add more features to your Trovebox site. Below is a list of all the available plugins you can activate and configure.
  </div>
</div>
<div class="row">
  <div class="span12">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Plugin Name</th>
          <th></th>
        </tr>
      </thead>
      <?php foreach($plugins as $plugin) { ?>
        <tr>
          <td><?php $this->utility->safe($plugin['name']); ?></td>
          <td>
            <div class="pull-right">
              <div class="<?php if($plugin['status'] === 'inactive') { ?>hide <?php } ?>active"><i class="icon-check icon-large"></i> Active (<a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/view" class="pluginView">Configure</a> or <a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/deactivate" class="pluginStatusToggle">Deactivate</a>)</div>
              <div class="<?php if($plugin['status'] === 'active') { ?>hide <?php } ?>inactive"><i class="icon-check-empty icon-large"></i> Inactive (<a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/activate" class="pluginStatusToggle">Activate</a>)</div>
            </div>
          </td>
        </tr>
      <?php } ?>
    </table>
  </div>
</div>

<a name="tokens"></a>
<div class="row">
  <div class="span12">
    <h2>Your sharing tokens</h2>
  </div>
</div>
<div class="row">
  <div class="span12">
    <table class="table table-striped">
      <thead>
        <tr>
          <th colspan="2">Photos</th>
        </tr>
      </thead>
      <?php if(count($tokens['photos']) === 0) { ?>
        <tr><td colspan="2">You don't have any sharing tokens for your photos.</td></tr>
      <?php } else { ?>
        <?php foreach($tokens['photos'] as $photo) { ?>
          <tr>
            <td>
              Photo <?php $this->utility->safe($photo['data']); ?>
              <small><em>(<?php if(empty($photo['dateExpires'])) { ?>Sharing token never expires<?php } else { ?>Sharing token expires on <?php $this->utility->dateLong($photo['dateExpires']); ?><?php } ?>)</em></small>
            </td>
            <td>
              <div class="pull-right">
                <a href="<?php $this->url->photoView($photo['data']); ?>"><i class="icon-eye-open icon-large"></i> View</a>
                &nbsp; &nbsp; &nbsp;
                <a href="/token/<?php $this->utility->safe($photo['id']); ?>/delete" class="tokenDelete"><i class="icon-trash icon-large"></i> Delete</a>
              </div>
            </td>
          </tr>
        <?php } ?>
      <?php } ?>
    </table>
    <table class="table table-striped">
      <thead>
        <tr>
          <th colspan="2">Albums</th>
        </tr>
      </thead>
      <?php if(count($tokens['albums']) === 0) { ?>
        <tr><td colspan="2">You don't have any sharing tokens for your albums.</td></tr>
      <?php } else { ?>
        <?php foreach($tokens['albums'] as $album) { ?>
          <tr>
            <td>
              Album
              <small><em>(<?php if(empty($album['dateExpires'])) { ?>Sharing token never expires<?php } else { ?>Sharing token expires on <?php $this->utility->dateLong($album['dateExpires']); ?><?php } ?>)</em></small>
            </td>
            <td>
              <div class="pull-right">
                <a href="<?php $this->url->albumView($album['data']); ?>"><i class="icon-eye-open icon-large"></i> View</a>
                &nbsp; &nbsp; &nbsp;
                <a href="/token/<?php $this->utility->safe($album['id']); ?>/delete" class="tokenDelete"><i class="icon-trash icon-large"></i> Delete</a>
              </div>
            </td>
          </tr>
        <?php } ?>
      <?php } ?>
    </table>
  </div>
</div>
