<?php $this->theme->display('partials/user-badge.php'); ?>
<div class="photo-grid">
  <div class="photo-grid-hr"></div>
</div>
<script> var initData = <?php echo json_encode($photos); ?>; var filterOpts = <?php echo json_encode($options); ?>;</script>
