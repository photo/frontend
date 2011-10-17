<div class="pagination">
  <?php if(!empty($pages)) { ?>
    <?php if(!isset($labelPosition) || $labelPosition == 'top') { ?>
		<p class="label">
			<span class="audible">Pagination:</span>
			Page <strong><?php echo $currentPage; ?></strong> of <strong><?php echo $totalPages; ?></strong>
		</p>
    <?php } ?>
	<ol class="sizechoose">
		<li class="empty">Size:</li>
		<li class="sizesmall"><a title="Show photos in size small"><span class="audible">Show photos in size small</span></a></li>
		<li class="sizemiddle on"><a title="Show photos in size middle"><span class="audible">Show photos in size middle</span></a></li>
		<li class="sizebig"><a title="Show photos in size big"><span class="audible">Show photos in size big</span></a></li>
	</ol>
    <ul role="navigation">
	<li class="empty">Current page:</li>
      <?php foreach($pages as $page) { ?>
        <?php if($page == $currentPage) { ?>
          <li class="on"><p><span class="audible">You're currently on page </span><?php echo $page; ?></p></li>
        <?php } else { ?>
          <li><a href="<?php echo preg_replace('#(/page-\d+)?/list#', '', $requestUri); ?>/page-<?php echo $page; ?>/list"><span class="audible">Page </span><?php echo $page; ?></a></li>
        <?php } ?>
      <?php } ?>
    </ul>
    <?php if(isset($labelPosition) && $labelPosition == 'bottom') { ?>
		<p class="label"><span class="audible">Pagination:</span>Page <strong><?php echo $currentPage; ?></strong> of <strong><?php echo $totalPages; ?></strong></p>
    <?php } ?>
  <?php } ?>
</div>
