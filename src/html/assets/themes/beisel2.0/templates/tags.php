<div class="tags">
  <header>
    <h1>Tags</h1>
    <div class="subnav">
      <ul class="nav nav-pills">
        <li><a href="/tags/list"><i class="icon-arrow-down icon-large"></i> A-Z</a></li>
        <li><a href="/tags/list?sortBy=id,desc"><i class="icon-arrow-up icon-large"></i> Z-A</a></li>
      </ul>
    </div>
  </header>

  <div class="row hero-unit tag-list <?php if(empty($tags)) { ?>empty<?php } ?>">
    <?php foreach($tags as $tag) { ?>
      <span class="label label-tag tag-<?php $this->utility->safe($tag['weight']); ?>">
        <a href="<?php $this->url->photosView(sprintf('tags-%s', $this->utility->safe($tag['id'], false))); ?>"><?php $this->utility->safe($tag['id']); ?></a>
      </span>
    <?php } ?>
  </div>
</div>
