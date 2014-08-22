<small>
  (
    <?php $this->utility->safe($photoCount); ?> photos
    <?php if($isAlbum && $this->permission->canUpload($album['id'])) { ?>
      <span class="hide"> | <a href="#" class="shareAlbum share trigger" data-id="<?php $this->utility->safe($album['id']); ?>" title="Share this album"><i class="icon-share"></i> Share</a></span>
    <?php } ?>

    &middot;

    sort:
    <?php if($sortParts[0] == 'dateTaken') { ?>
      <i class="icon-calendar" title="Sorted by date taken"></i>
    <?php } else { ?>
      <a href="?sortBy=<?php $this->utility->safe($this->utility->getSortByParams('by','dateTaken',$currentSortBy, false)); ?>" title="Sort by date date taken"><i class="icon-calendar"></i></a>
    <?php } ?>
    or
    <?php if($sortParts[0] == 'dateUploaded') { ?>
      <i class="icon-upload" title="Sorted by date uploaded"></i>
    <?php } else { ?>
      <a href="?sortBy=<?php $this->utility->safe($this->utility->getSortByParams('by','dateUploaded',$currentSortBy, false)); ?>" title="Sort by date uploaded"><i class="icon-upload"></i></a>
    <?php } ?>

    &middot;

    order:
    <?php if($sortParts[1] == 'asc') { ?>
      <i class="icon-sort-by-order" title="Ordered first to last"></i>
    <?php } else { ?>
      <a href="?sortBy=<?php $this->utility->safe($this->utility->getSortByParams('sort','asc',$currentSortBy, false)); ?>" title="Order first to last"><i class="icon-sort-by-order"></i></a>
    <?php } ?>
    or
    <?php if($sortParts[1] == 'desc') { ?>
      <i class="icon-sort-by-order-alt" title="Orderest last to first"></i>
    <?php } else { ?>
      <a href="?sortBy=<?php $this->utility->safe($this->utility->getSortByParams('sort','desc',$currentSortBy, false)); ?>" title="Order last to first"><i class="icon-sort-by-order-alt"></i></a>
    <?php } ?>
  )
  <?php if($pages['currentPage'] > 1) { ?>
    <span class="pagination">
      <?php //$this->utility->safe($pages['currentPage']); ?>
      <ul>
        <li><i class="icon-list-ol" title="Prior pages"></i></li>
        <?php $thisPage = max($pages['currentPage']-5, 1); ?>
        <?php if($thisPage > 1) { ?>
          <li><a href="<?php $this->utility->getPaginationUrl($uri, 1, '/photos'); ?>">1</a></li>
          <li class="active"><a href="#">...</a></li>
        <?php } ?>

        <?php do { ?>
          <li><a href="<?php $this->utility->getPaginationUrl($uri, $thisPage, '/photos'); ?>"><?php $this->utility->safe($thisPage); ?></a></li>
          <?php $thisPage++; ?>
        <?php }while($thisPage < $pages['currentPage']); ?>
        <li class="active"><a href="#"><?php $this->utility->safe($pages['currentPage']); ?></a></li>
        <li class="disabled"><em>of <?php $this->utility->safe($pages['totalPages']); ?> pages</em></li>
      </ul>
    </span>
  <?php } ?>
</small>
