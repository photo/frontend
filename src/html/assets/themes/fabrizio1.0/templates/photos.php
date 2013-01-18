<?php $this->theme->display('partials/user-badge.php'); ?>


<div class="photo-grid">
  <div class="photo-grid-hr"></div>
  <?php if(isset($album)) { ?>
    <h4><i class="icon-th-large"></i> <?php $this->utility->safe($album['name']); ?> <small>(<?php $this->utility->safe($album['count']); ?> photos)</small></h4>
  <?php } ?>
</div>
<script> var initData = <?php echo json_encode($photos); ?>; var filterOpts = <?php echo json_encode($options); ?>;</script>
