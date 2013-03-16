<li class="type-<?php $this->utility->safe($activity[0]['type']); ?>">
  <div class="activity-meta">
    <span class="activity-time"><?php $this->utility->timeAsText($activity[0]['data']['dateUploaded']); ?></span>
    <i class="activity-type icon-upload"></i>
  </div>
  <?php if(stristr($avatar = $this->user->getAvatarFromEmail(50, $activity[0]['owner']), 'gravatar.com') === false) { ?>
    <img class="to" src="<?php $this->utility->safe($avatar); ?>" alt="<?php $this->utility->safe($this->user->getNameFromEmail($activity[0]['owner'])); ?>">
  <?php } else { ?>
    <i class="to icon-user" alt="<?php $this->utility->safe($this->user->getNameFromEmail($activity[0]['owner'])); ?>"></i>
  <?php } ?>
  <div class="activity-wrap">
    <div class="activity-content">
      <strong><?php $this->utility->safe($this->user->getNameFromEmail($activity[0]['owner'])); ?></strong> uploaded <?php printf('%d %s', count($activity), $this->utility->plural(count($activity), 'photo', false)); ?>
      <ul class="unstyled">
        <?php foreach($activity as $activityDetails) { ?>
          <li>
            <a href="<?php echo $activityDetails['data']['url']; ?>">
              <img src="<?php $this->utility->safe($activityDetails['data']['path100x100xCR']); ?>" class="activityfeed-thumbnail">
            </a>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</li>
