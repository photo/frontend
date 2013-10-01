<div class="row">
  <div class="span12 tags-content">
    <?php if(!empty($tags)) { ?>
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
