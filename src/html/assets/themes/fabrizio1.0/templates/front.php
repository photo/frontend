<?php $this->theme->display('partials/user-badge.php'); ?>
<?php if(!empty($activities)) { ?>
  <ol>
    <?php foreach($activities as $activity) { ?>
        <?php $this->theme->display(sprintf('partials/feed-%s.php', $activity[0]['type']), array('activity' => $activity)); ?>
      <?php $i++; ?>
    <?php } ?>
  </ol>
<?php } ?>
