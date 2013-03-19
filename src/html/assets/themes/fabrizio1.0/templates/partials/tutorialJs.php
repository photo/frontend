TBX.tutorial.queue(
  '<?php $this->utility->safe($step); ?>', 
  '<?php $this->utility->safe($selector); ?>', 
  '<?php echo $intro; // don't safe so we can include html ?>', 
  '<?php $this->utility->safe($key); ?>', 
  '<?php $this->utility->safe($section); ?>', 
  <?php if(isset($width)) { printf("'%s'", $this->utility->safe($width, false)); } else { echo 'null'; } ?>,
  <?php $this->utility->safe(isset($init) ? json_encode($init) : 'false'); ?>
);
