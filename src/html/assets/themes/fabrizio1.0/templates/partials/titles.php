<?php
  $user = new User;
  $utilityObj = new Utility;
  $page = $this->plugin->getData('page');
  $username = $utilityObj->safe($user->getNameFromEmail($this->config->user->email), false);
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
