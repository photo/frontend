<?php
  $user = new User;
  $utilityObj = new Utility;
  $page = $this->plugin->getData('page');
  $username = $utilityObj->safe($user->getNameFromEmail($this->config->user->email), false);
?>

<title>
<?php if($page === 'photo-detail') { ?>
  <?php /* done in JS */ ?>
<?php } elseif($page === 'photos') { ?>
  <?php $album = $this->plugin->getData('album'); ?>
  <?php $tags = $this->plugin->getData('tags'); ?>
  <?php if($album) { ?>
    <?php $this->utility->safe(sprintf("%s / Album / %s", $username, $album['name'])); ?>
  <?php } elseif($tags) { ?>
    <?php $this->utility->safe(sprintf("%s / Tags / %s", $username, implode(', ', $tags))); ?>
  <?php } else { ?>
    <?php $this->utility->safe(sprintf("%s / Photos", $username)); ?>
  <?php } ?>
<?php } elseif($page === 'albums') { ?>
  <?php $this->utility->safe(sprintf("%s / Album", $username)); ?>
<?php } elseif($page === 'tags') { ?>
  <?php $this->utility->safe(sprintf("%s / Tags", $username)); ?>
<?php } else { ?>
  <?php $this->utility->safe(sprintf("%s", $username)); ?>
<?php } ?>
 / Trovebox
</title>
