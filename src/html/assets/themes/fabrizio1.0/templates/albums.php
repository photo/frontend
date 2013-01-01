<?php if(!empty($albums)) { ?>
<ul class="albums"></ul>
<script> var initData = <?php echo json_encode($albums); ?>; var filterOpts = <?php echo json_encode($options); ?>;</script>
<?php } else { ?>
  No albums
<?php } ?>
