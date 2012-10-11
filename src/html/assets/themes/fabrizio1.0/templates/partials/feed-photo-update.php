The following photos were updated.
<?php foreach($activities as $activity) { ?>
  <img src="<?php $this->url->photoUrl($activity['data'], '100x100xCR'); ?>" width="50" height="50">
<?php } ?>
