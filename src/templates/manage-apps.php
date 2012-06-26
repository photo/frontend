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
        <td><?php $this->utility->safe($credential['name']); ?></td>
        <td><a href="/oauth/<?php $this->utility->safe($credential['id']); ?>/delete" class="credential-delete-click"><i class="icon-ban-circle icon-large"></i> Revoke</a></td>
      </tr>
    <?php } ?>
  </table>
</div>
