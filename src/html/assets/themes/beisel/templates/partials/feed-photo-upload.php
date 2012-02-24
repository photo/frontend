The following photos were uploaded.
<?php foreach($activities as $activity) { ?>
  <a href="<?php $this->url->photoView($activity['data']['id']); ?>"><img src="<?php $this->url->photoUrl($activity['data'], '100x100xCR'); ?>" width="50" height="50"></a>
<?php } ?>
