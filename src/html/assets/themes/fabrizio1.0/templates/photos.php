<?php $this->theme->display('partials/user-badge.php'); ?>

<div class="photo-grid">
  <div class="photo-grid-hr"></div>
  <?php if(!empty($photos)) { ?>
    <?php if(isset($album)) { ?>
      <h4><i class="icon-th-large"></i> <?php $this->utility->safe($album['name']); ?> <small>(<?php $this->utility->safe($album['count']); ?> photos)</small></h4>
    <?php } ?>
    <script> var initData = <?php echo json_encode($photos); ?>; var filterOpts = <?php echo json_encode($options); ?>;</script>
  <?php } else { ?>
    <?php if($this->user->isAdmin()) { ?>
      <h4>You haven't uploaded any photos, yet.</h4>  
      <p>
        It's easy to start uploading photos. Head over to the <a href="/photos/upload">upload</a> page to get started.
      </p>
    <?php } else { ?>
      <h4>This user hasn't uploaded any photos, yet.</h4>  
      <p>
        You should give them a nudge to get started!
      </p>
    <?php } ?>
  <?php } ?>
</div>
