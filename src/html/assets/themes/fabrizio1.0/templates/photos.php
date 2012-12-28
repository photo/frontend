<?php $this->theme->display('partials/user-badge.php'); ?>
<div class="photo-grid">
  <div class="photo-grid-hr"></div>
</div>
<script type="tmpl/underscore" id="photo-meta">
  <div class="photo-meta">
    <h4 class="title"><a href="/p/<%= id %>"><%= title || filenameOriginal %></a></h4>
    <ul class="info">
      <li><a href="#"><i class="tb-icon-small-comment tb-icon-dark"></i> <span class="number">24</span></a>
      <li><a href="#"><i class="tb-icon-small-heart tb-icon-dark"></i> <span class="number">24</span></a>
      <li><a href="#"><i class="tb-icon-small-maximize tb-icon-dark"></i> <span class="number">Share</span></a>
      <li><a href="#"><i class="tb-icon-small-<%= permission == 0 ? 'locked' : 'unlocked' %> tb-icon-dark permission" data-id="<%= id %>"></i></a>
    </ul>
  </div>
</script>
<script> var initData = <?php echo json_encode($photos); ?>; var filterOpts = <?php echo json_encode($options); ?>;</script>
