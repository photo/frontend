<?php for($i = 1; $i <= $totalPages; $i++) { ?>
  <a href="<?php echo preg_replace('#/page-\d+#', '', $requestUri); ?>/page-<?php echo $i; ?>"><?php echo $i; ?></a>, 
<?php } ?>

