<div class="manage features">
  <a name="settings"></a>
  <div class="row hero-unit blurb">
    <h2>Configure your Trovebox site</h2>
    <p>
      Don't want users to download your original photos? No problem. Want to allow the same photo to be uploaded twice? You're at the right spot.
    </p>    
  </div>
  
  <form method="post" action="/manage/settings">
    <h3>General settings</h3>
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
    
    <div class="btn-toolbar"><button class="btn btn-primary">Save</button></div>
    <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
  </form>

  <a name="apps"></a>
  <div class="row hero-unit blurb">
    <h2>Your OAuth Applications</h2>
    <p>
      You've granted these applications access to your Trovebox account. Clicking <strong>revoke</strong> cannot be undone and you may have to reapprove the application.
    </p>
    <p>
      <a href="/v1/oauth/authorize?oauth_callback=<?php $this->utility->safe(sprintf('%s://%s%s', $this->utility->getProtocol(false), $_SERVER['HTTP_HOST'], '/manage/apps/callback')); ?>&name=<?php $this->utility->safe(urlencode('Self Generated App')); ?>&tokenType=access" class="btn btn-primary">Create a new app</a>
    </p>
  </div>
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
              <i class="icon-eye-open icon-large"></i> <a href="/v1/oauth/<?php $this->utility->safe($credential['id']); ?>/view" class="credential-view-click" data-controls-modal="modal">View</a>
              &nbsp;
              <i class="icon-trash icon-large"></i> <a href="/oauth/<?php $this->utility->safe($credential['id']); ?>/delete" class="credentialDelete">Revoke</a>
            </div>
          </td>
        </tr>
      <?php } ?>
    </table>
  <?php } ?>
  
  <a name="plugins"></a>
  <div class="row hero-unit blurb">
    <h2>Your plugins</h2>
    <p>
      Plugins help you add more features to your Trovebox site. Below is a list of all the available plugins you can activate and configure.
    </p>
  </div>
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
            <div class="<?php if($plugin['status'] === 'inactive') { ?>hide <?php } ?>active"><i class="icon-check icon-large"></i> Active (<a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/view" class="plugin-view-click">Configure</a> or <a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/deactivate" class="plugin-status-toggle-click">Deactivate</a>)</div>
            <div class="<?php if($plugin['status'] === 'active') { ?>hide <?php } ?>inactive"><i class="icon-check-empty icon-large"></i> Inactive (<a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/activate" class="plugin-status-toggle-click">Activate</a>)</div>
          </div>
        </td>
      </tr>
    <?php } ?>
  </table>
</div>
