<div class="pagination">
  <?php if($totalPages > 1) { ?>
    <div class="label">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></div>
    <ol>
      <?php for($i = 1; $i <= $totalPages; $i++) { ?>
        <li <?php if($i == $currentPage) { ?>class="on"<?php } ?>><a href="<?php echo preg_replace('#/page-\d+#', '', $requestUri); ?>/page-<?php echo $i; ?>"><?php echo $i; ?></a></li>
      <?php } ?>
    </ol>
  <?php } ?>
</div>
<br clear="all">
