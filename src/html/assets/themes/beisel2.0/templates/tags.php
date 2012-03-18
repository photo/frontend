<div class="tags">
  <header>
    <h1>Tags</h1>
    <div class="subnav">
      <ul class="nav nav-pills">
        <li><a href="#"><i class="icon-arrow-down icon-large"></i> A-Z</a></li>
        <li><a href="#"><i class="icon-arrow-up icon-large"></i> Z-A</a></li>
        <li class="last">
          <form action="">
            <div class="input-append">
              <input class="search-query span2" id="appendedInput" size="16" type="text" data-provide="typeahead" />
              <a href="" class="add-on"><i class="icon-search icon-large"></i></a>
            </div>
          </form>
        </li>
      </ul>
    </div>
  </header>

  <div class="row hero-unit tag-list <?php if(empty($tags)) { ?>empty<?php } ?>">
    <?php foreach($tags as $tag) { ?>
      <span class="label label-tag">
        <a href="<?php $this->url->photosView(sprintf('tags-%s', $this->utility->safe($tag['id'], false))); ?>"><?php $this->utility->safe($tag['id']); ?></a>
      </span>
    <?php } ?>
  </div>
</div>
