<div class="row">
  <div class="span12 album-grid">
    <?php if(!empty($albums) ) { ?>
      <h4><i class="icon-th-large"></i> Albums <small>(<?php $this->utility->safe($albums[0]['totalRows']); ?> total &middot; <i class="icon-sort-by-alphabet"></i>)</small></h4>
      <ul class="albums"></ul>
      <script> var initData = <?php echo json_encode($albums); ?>;</script>
    <?php } else { ?>
      <?php if($this->user->isAdmin()) { ?>
        <h4>You haven't created any albums, yet.</h4>  
        <p>
          It's easy to get started with albums.
          <ol>
            <li>Click the link above to create a new album.</li>
            <li>Type a name and create the album.</li>
            <li>Head over to your gallery and select the photos you'd like to add.</li>
            <li>Add them to your newly created album.</li>
          </ol>
        </p>
      <?php } else { ?>
        <h4>This user hasn't created any albums, yet.</h4>  
        <p>
          You should give them a nudge to get started!
        </p>
      <?php } ?>
    <?php } ?>
  </div>
</div>
<?php if(!empty($albums)) { ?>
  <div class="row">
    <div class="span12 loadMoreContainer">
      <button class="btn btn-theme-secondary loadMore loadMoreAlbums hide"><i></i> Load more</button>
    </div>
  </div>
<?php } ?>
