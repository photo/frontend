<?php foreach($activities as $activity) { ?>
  <?php if($activity['data']['action']['type'] == 'comment')  {?>
    <?php $this->utility->safe($activity['data']['action']['value']); ?>
    <img src="<?php $this->url->photoUrl($activity['data']['target'], '100x100xCR'); ?>" width="50" height="50">
  <?php } ?>
<?php } ?>
