<?php $this->theme->display('partials/user-badge.php'); ?>

<div class="row">
  <div class="span12 photo-grid <?php if(isset($album)) { ?>is-album<?php } ?>">
    <div class="photo-grid-hr"></div>
    <?php if(isset($album)) { ?>
    <h4>
      <i class="icon-th-large"></i>
      <?php $this->utility->safe($album['name']); ?>
      <small>
        (
          <?php $this->utility->safe($album['count']); ?> photos
          <?php if($this->user->isAdmin()) { ?>
            <span class="hide"> | <a href="#" class="shareAlbum share trigger" data-id="<?php $this->utility->safe($album['id']); ?>" data-name="<?php $this->utility->safe($album['name']); ?>" title="Share this album"><i class="icon-share"></i> Share</a></span>
          <?php } ?>
        )
      </small>
    </h4>
    <?php } else if(isset($tags)) { ?>
      <h4><i class="icon-tags"></i> <?php $this->utility->safe(implode(', ', $tags)); ?> <small>(<?php $this->utility->safe($photos[0]['totalRows']); ?> photos)</small></h4>
    <?php } ?>
    <?php if(!empty($photos)) { ?>
      <script> var initData = <?php echo json_encode($photos); ?>; var filterOpts = <?php echo json_encode($options); ?>;</script>
    <?php } else { ?>
      <?php if($this->user->isAdmin()) { ?>
        <h4>No photos to show.</h4>  
        <p>
          It's easy to start uploading photos. Head over to the <a href="/photos/upload">upload</a> page to get started.
        </p>
      <?php } else { ?>
        <h4>This user hasn't uploaded any photos, yet.</h4>  
        <p>
          You should give them a nudge to get started! If this is your site then you can <a href="/user/login?r=<?php $this->utility->safe(urlencode($_SERVER['REQUEST_URI'])); ?>">log in</a>.
        </p>
      <?php } ?>
    <?php } ?>
  </div>
</div>
<?php if(!empty($photos)) { ?>
  <div class="row">
    <div class="span12 loadMoreContainer">
      <button class="btn btn-theme-secondary loadMore loadMorePhotos hide"><i></i> Load more</button>
    </div>
  </div>
<?php } ?>
