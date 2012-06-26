<?php if(!empty($pages)) { ?>
  <div class="pagination">
    <a href="#" class="pin-select-all-click pin-select-all"><i class="icon-ok icon-large"></i> Select all</a>
    <ul role="navigation">
      <li>
        <?php $page = 1; ?>
        <?php if(strstr($requestUri, '?') === false) { ?>
          <a href="<?php $this->utility->safe(sprintf('%s?page=%d', $requestUri, $page)); ?>">
        <?php } elseif(strstr($requestUri, '/list') !== false && !isset($_GET['page'])) { ?>
          <a href="<?php $this->utility->safe(preg_replace('#(/page-\d+)?/list#', "/page-{$page}/list", $requestUri)); ?>">
        <?php } else { ?>
          <a href="<?php $this->utility->safe(sprintf('%s&page=%d', preg_replace('#&?page=\d+#', '', $requestUri), $page)); ?>">
        <?php } ?>
        &larr; First</a>
      </li>
      <?php foreach($pages as $page) { ?>
        <li <?php if($page == $currentPage) { ?>class="active"<?php } ?>>
          <?php if(strstr($requestUri, '?') === false) { ?>
            <a href="<?php $this->utility->safe(sprintf('%s?page=%d', $requestUri, $page)); ?>">
          <?php } elseif(strstr($requestUri, '/list') !== false && !isset($_GET['page'])) { ?>
            <a href="<?php $this->utility->safe(preg_replace('#(/page-\d+)?/list#', "/page-{$page}/list", $requestUri)); ?>">
          <?php } else { ?>
            <a href="<?php $this->utility->safe(sprintf('%s&page=%d', preg_replace('#&?page=\d+#', '', $requestUri), $page)); ?>">
          <?php } ?>
            <span class="audible">Page </span>
            <?php echo $page; ?>
          </a>
        </li>
      <?php } ?>
      <li>
        <?php $page = $totalPages; ?>
        <?php if(strstr($requestUri, '?') === false) { ?>
          <a href="<?php $this->utility->safe(sprintf('%s?page=%d', $requestUri, $page)); ?>">
        <?php } elseif(strstr($requestUri, '/list') !== false && !isset($_GET['page'])) { ?>
          <a href="<?php $this->utility->safe(preg_replace('#(/page-\d+)?/list#', "/page-{$page}/list", $requestUri)); ?>">
        <?php } else { ?>
          <a href="<?php $this->utility->safe(sprintf('%s&page=%d', preg_replace('#&?page=\d+#', '', $requestUri), $page)); ?>">
        <?php } ?>
        &rarr; Last</a></li>
    </ul>
  </div>
<?php } ?>
