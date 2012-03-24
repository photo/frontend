<?php if(!empty($pages)) { ?>
  <?php if(!isset($simple) || $simple === false) { ?>
    <div class="pagination">
      <ul role="navigation">
        <?php foreach($pages as $page) { ?>
          <li <?php if($page == $currentPage) { ?>class="active"<?php } ?>>
            <?php if(strstr($requestUri, '/list') !== false) { ?>
              <a href="<?php $this->utility->safe(preg_replace('#(/page-\d+)?/list#', "/page-{$page}/list", $requestUri)); ?>">
            <?php } elseif(strstr($requestUri, '?') === false) { ?>
              <a href="<?php $this->utility->safe(sprintf('%s?page=%d', $requestUri, $page)); ?>">
            <?php } else { ?>
              <a href="<?php $this->utility->safe(sprintf('%s&page=%d', preg_replace('#&?page=\d+#', '', $requestUri), $page)); ?>">
            <?php } ?>
              <span class="audible">Page </span>
              <?php echo $page; ?>
            </a>
          </li>
        <?php } ?>
      </ul>
    </div>
  <?php } else { ?>
    <?php foreach($pages as $page) { ?>
      <?php if(strstr($requestUri, '/list') !== false) { ?>
        <a href="<?php $this->utility->safe(preg_replace('#(/page-\d+)?/list#', "/page-{$page}/list", $requestUri)); ?>">
      <?php } elseif(strstr($requestUri, '?') === false) { ?>
        <a href="<?php $this->utility->safe(sprintf('%s?page=%d', $requestUri, $page)); ?>">
      <?php } else { ?>
        <a href="<?php $this->utility->safe(sprintf('%s&page=%d', preg_replace('#&?page=\d+#', '', $requestUri), $page)); ?>">
      <?php } ?>
        <span class="audible">Page </span>
        <?php echo $page; ?>
      </a>
    <?php } ?>
  <?php } ?>
<?php } ?>
