<div class="row">
  <div class="span8">
    <?php if($this->user->isAdmin()) { ?>
      <div class="userbadge user-badge-meta"data-show-storage="true"></div>
    <?php } else { ?>
      <div class="userbadge user-badge-meta"></div>
    <?php } ?>
    <h3 class="sidebar-heading activity-list-heading">
      <i class="icon-angle-down"></i>
      <i class="icon-inbox"></i>
      Latest Activity
    </h3>
    <?php if(!empty($activities)) { ?>
      <ul class="activity-list">
        <?php foreach($activities as $activity) { ?>
          <?php if($activity[0]['type'] == 'photo-upload') { ?>
            <?php $this->theme->display(sprintf('partials/feed-%s.php', $activity[0]['type']), array('activity' => $activity)); ?>
          <?php } ?>
        <?php } ?>
      </ul>
    <?php } else { ?>
      <h4>No activity. Create some buzz by uploading a photo.</h4>
    <?php } ?>
  </div>
  <div class="span4">
    <ul class="sidebar">
      <li>
        <h3>OpenPhoto Links</h3>
        <p><a href="https://github.com/photo">All our source are belong to you</a></p>
        <p><a href="https://twitter.com/trovebox">Follow us on Twitter</a></p>
      </li>
    </ul>
  </div>
</div>
