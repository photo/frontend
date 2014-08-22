<div class="row">
  <div class="span12 tags-content">
    <?php if(!empty($tags)) { ?>
      <h4><i class="icon-tags"></i> Tags <small>(<?php $this->utility->safe(count($tags)); ?> total &middot; <i class="icon-sort-by-alphabet"></i>)</small></h4>
      <ul class="tags">
        <?php foreach($tags as $tag) { ?>
          <li><a href="/photos/tags-<?php $this->utility->safe($tag['id']); ?>/list"><?php $this->utility->safe($tag['id']); ?></a></li>
        <?php } ?>
      </ul>
    <?php } else { ?>
        <h4>This user hasn't created any tags, yet.</h4>  
        <p>
          You should give them a nudge to get started!
        </p>
    <?php } ?>
  </div>
</div>
