<div class="manage applications">
  <div class="row hero-unit blurb">
    <h2>Your OAuth Applications</h2>
    <p>
      You've granted these applications access to your OpenPhoto account. Clicking <strong>revoke</strong> cannot be undone and you may have to reapprove the application.
    </p>
    <p>
      <a href="/v1/oauth/authorize?oauth_callback=<?php $this->utility->safe(sprintf('%s://%s%s', $this->utility->getProtocol(false), $_SERVER['HTTP_HOST'], '/manage/apps/callback')); ?>&name=<?php $this->utility->safe(urlencode('Self Generated App')); ?>" class="btn btn-primary">Create a new app</a>
    </p>
  </div>
  <table class="table well">
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
            <em class="credential-date"> created on <?php $this->utility->dateLong($credential['dateCreated']); ?></em>
          <?php } ?>
        </td>
        <td><a href="/oauth/<?php $this->utility->safe($credential['id']); ?>/delete" class="credential-delete-click"><i class="icon-remove icon-large"></i> Revoke</a></td>
      </tr>
    <?php } ?>
  </table>
  
  <div class="row hero-unit blurb">
    <h2>Your plugins</h2>
    <p>
      Plugins help you add more features to your OpenPhoto site. Below is a list of all the available plugins you can activate and configure.
    </p>
  </div>
  <table class="table well">
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
          <div class="<?php if($plugin['status'] === 'inactive') { ?>hide <?php } ?>active"><i class="icon-check icon-large"></i> Active (<a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/view" class="plugin-view-click">Configure</a> or <a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/deactivate" class="plugin-status-toggle-click">Deactivate</a>)</div>
          <div class="<?php if($plugin['status'] === 'active') { ?>hide <?php } ?>inactive"><i class="icon-check-empty icon-large"></i> Inactive (<a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/activate" class="plugin-status-toggle-click">Activiate</a>)</div>
        </td>
      </tr>
    <?php } ?>
  </table>
</div>
