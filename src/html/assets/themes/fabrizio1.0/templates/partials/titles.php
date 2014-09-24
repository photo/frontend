<?php
  $user = new User;
  $utilityObj = new Utility;
  $configObj = getConfig();
  $page = $this->plugin->getData('page');

  // since $this->config->user doesn't exist on first set up we have to check gh-1546
  if(isset($this->config->user)) {
    $username = $utilityObj->safe($user->getNameFromEmail($this->config->user->email), false);
  } else {
    $username = User::displayNameDefault;
  }
  $title = '';
?>

<?php if($page === 'photo-detail') { ?>
  <?php $photo = $this->plugin->getData('photo'); ?>
  <?php $photoTitle = !empty($photo['name']) ? $photo['name'] : $photo['filenameOriginal']; ?>
  <?php $title = sprintf("%s / Photo / %s", $username, $photoTitle); ?>
<?php } elseif($page === 'photos') { ?>
  <?php $album = $this->plugin->getData('album'); ?>
  <?php $tags = $this->plugin->getData('tags'); ?>
  <?php if($album) { ?>
    <?php $title = sprintf("%s / Album / %s", $username, $album['name']); ?>
  <?php } elseif($tags) { ?>
    <?php $title = sprintf("%s / Tags / %s", $username, implode(', ', $tags)); ?>
  <?php } else { ?>
    <?php $title = sprintf("%s / Photos", $username); ?>
  <?php } ?>
<?php } elseif($page === 'albums') { ?>
  <?php $title = sprintf("%s / Album", $username); ?>
<?php } elseif($page === 'tags') { ?>
  <?php $title = sprintf("%s / Tags", $username); ?>
<?php } else { ?>
  <?php $title = sprintf("%s", $username); ?>
<?php } ?>

<?php $title .= ' / Trovebox'; ?>

<title data-original="<?php $this->utility->safe($title); ?>"><?php $this->utility->safe($title); ?></title>
