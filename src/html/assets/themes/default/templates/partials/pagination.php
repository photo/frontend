<div class="pagination">
  <?php if(!empty($pages)) { ?>
    <?php if(!isset($labelPosition) || $labelPosition == 'top') { ?>
      <p class="label"><span class="audible">Pagination:</span>Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></p>
    <?php } ?>
    <ul role="navigation">
      <?php foreach($pages as $page) { ?>
        <?php if($page == $currentPage) { ?>
          <li class="on"><p><span class="audible">You're currently on page </span><?php echo $page; ?></p></li>
        <?php } else { ?>
          <li><a href="<?php echo preg_replace('#(/page-\d+)?/list#', '', $requestUri); ?>/page-<?php echo $page; ?>/list"><span class="audible">Page </span><?php echo $page; ?></a></li>
        <?php } ?>
      <?php } ?>
    </ul>
    <?php if(isset($labelPosition) && $labelPosition == 'bottom') { ?>
      <p class="label"><span class="audible">Pagination:</span>Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></p>
    <?php } ?>
  <?php } ?>
</div>
