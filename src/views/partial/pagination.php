<div class="pagination">
  <?php if($totalPages > 1) { ?>
    <?php if(!isset($labelPosition) || $labelPosition == 'top') { ?>
      <div class="label">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></div>
    <?php } ?>
    <ol>
      <?php for($i = 1; $i <= $totalPages; $i++) { ?>
        <li <?php if($i == $currentPage) { ?>class="on"<?php } ?>><a href="<?php echo preg_replace('#/page-\d+#', '', $requestUri); ?>/page-<?php echo $i; ?>"><?php echo $i; ?></a></li>
      <?php } ?>
    </ol>
    <?php if(isset($labelPosition) && $labelPosition == 'bottom') { ?>
      <div class="label">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></div>
    <?php } ?>
  <?php } ?>
</div>
<br clear="all">
