<div class="pagination">
  <?php if($totalPages > 1) { ?>
    <?php if(!isset($labelPosition) || $labelPosition == 'top') { ?>
      <p class="label"><span class="audible">Pagination:</span>Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></p>
    <?php } ?>
    <ul role="navigation">
      <?php for($i = 1; $i <= $totalPages; $i++) { ?>
        <?php if($i == $currentPage) { ?>
          <li class="on"><p><span class="audible">You're currently on page </span><?php echo $i; ?></p></li>
        <?php } else { ?>
          <li><a href="<?php echo preg_replace('#(/page-\d+)?/list#', '', $requestUri); ?>/page-<?php echo $i; ?>/list"><span class="audible">Page </span><?php echo $i; ?></a></li>
        <?php } ?>
      <?php } ?>
    </ul>
    <?php if(isset($labelPosition) && $labelPosition == 'bottom') { ?>
      <p class="label"><span class="audible">Pagination:</span>Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></p>
    <?php } ?>
  <?php } ?>
</div>
