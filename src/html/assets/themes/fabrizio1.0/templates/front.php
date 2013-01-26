<div class="row">
  <div class="span8">
    <div class="userbadge user-badge-meta"></div>
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
        <h3>Download the App!</h3>
        <p>Take OpenPhoto wherever you go&mdash;download our app for iPhone or Android</p>
        <p>
          <a href="#"><img src="<?php echo $this->theme->asset('image', 'download-ios.jpg') ?>" /></a>
        </p>
        <p>
          <a href="#"><img src="<?php echo $this->theme->asset('image', 'download-android.jpg') ?>" /></a>
        </p>
      </li>
      <li>
        <h3>OpenPhoto Links</h3>
        <p><a href="https://github.com/photo">All our source are belong to you</a></p>
        <p><a href="http://blog.theopenphotoproject.org">Keep up to date on our blog</a></p>
        <p><a href="https://twitter.com/trovebox">Follow us on Twitter</a></p>
      </li>
    </ul>
  </div>
</div>
