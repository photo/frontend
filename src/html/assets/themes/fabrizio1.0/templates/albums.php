<?php if(!empty($albums) ) { ?>
<ul class="albums"></ul>
<script> var initData = <?php echo json_encode($albums); ?>;</script>
<?php } else { ?>
  No albums
<?php } ?>
