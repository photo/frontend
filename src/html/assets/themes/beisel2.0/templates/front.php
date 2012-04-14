<?php if($photos[0]['totalRows'] > 0) { ?>
  <?php if(!empty($activities)) { ?>
    <!-- activity feed -->
    <div class="row activityfeed-startpage">
      <div class="span12">
        <div id="feedCarousel" class="carousel feed">
          <div class="span3">
            <h2>Latest activity</h2>
            <?php if(count($activities) > 1) { ?>
              <div class="carousel-control feed">
                <a class="left btn" href="#feedCarousel" data-slide="prev"><i class="icon-chevron-left icon-large"></i></a>
                <a class="right btn" href="#feedCarousel" data-slide="next"><i class="icon-chevron-right icon-large"></i></a>
              </div>
            <?php } ?>
          </div>
          <div class="carousel-inner span9">
            <?php $i = 0; ?>
            <?php foreach($activities as $activity) { ?>
              <div class="item <?php if($i == 0) { ?>active<?php } ?>">
                <?php $this->theme->display(sprintf('partials/feed-%s.php', $activity[0]['type']), array('activity' => $activity)); ?>
              </div>
              <?php $i++; ?>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>

  <?php if($photos[0]['totalRows'] == 0) { ?>
  <?php } else { ?>
    <!-- carousel -->
    <div id="homeCarousel" class="row hero-unit carousel front">
      <!-- large photo -->
      <div class="carousel-inner">
        <?php foreach($photos as $i => $photo) { ?>
          <div class="item <?php if($i == 0) { ?>active<?php } ?> row" data-index="<?php echo $i; ?>">
            <a href="<?php $this->url->photoView($photo['id']); ?>" class="span9"><img src="<?php $this->url->photoUrl($photo, '870x652xCR'); ?>" alt="<?php $this->utility->safe($photo['title']); ?>" /></a>
            <div class="span3">
              <h1>
                <a href="<?php $this->url->photoView($photo['id']); ?>">
                  <?php if(empty($photo['title'])) { ?>
                    <?php $this->utility->safe($photo['filenameOriginal']); ?>
                  <?php } else { ?>
                    <?php $this->utility->safe($photo['title']); ?>
                  <?php } ?>
                </a>
              </h1>
              <p>This photo was taken <?php $this->utility->safe($this->utility->timeAsText($photo['dateTaken'])); ?></p>
              <?php if(isset($photo['longitude']) && !empty($photo['longitude'])) { ?>
                <div class="map">
                  <a href="<?php $this->url->photoView($photo['id']); ?>" class="invert"><i class="icon-map-marker"></i> <?php printf('%s, %s', $this->utility->safe($photo['longitude'], false), $this->utility->safe($photo['latitude'], false)); ?></a>
                  <a href="<?php $this->url->photoView($photo['id']); ?>"><img src="<?php $this->utility->staticMapUrl($photo['latitude'], $photo['longitude'], 10, '250x100'); ?>"></a>
                </div>
              <?php } ?>
              <div class="iconbox">
                <a href="" class="invert"><i class="icon-comment"></i> <?php if(!isset($photos['actions'])) { ?>0<?php } else { $this->utility->safe(count($photos['actions'])); } ?> comments &amp; favorites</a>
                <!--<a href="" class="invert"><i class="icon-signal"></i> 154 visits</a><br />
                <a href="" class="invert"><i class="icon-folder-close"></i> path photos</a>-->
              </div>
              <?php if(count($photo['tags']) > 0) { ?>
                <div class="tags">
                  <?php foreach($photo['tags'] as $tag) { ?>
                    <a href="<?php $this->url->photosView(sprintf('tags-%s', $this->utility->safe($tag, false))); ?>" class="label label-tag"><?php $this->utility->safe($tag); ?></a>
                  <?php } ?>
                </div>
              <?php } ?>
              <div class="social">
                <div id="likebutton">
                  <?php if($this->plugin->isActive('FacebookConnect')) {?>
                    <div class="facebook">
                      <div class="fb-like" data-href="<?php $this->utility->safe(sprintf('%s://%s%s', $this->utility->getProtocol(false), $_SERVER['HTTP_HOST'], $this->url->photoView($photo['id'], null, false))); ?>" data-font="lucida grande"></div>
                    </div>
                  <?php } ?>
                  <!--<div class="twitter"><a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.openphoto.me" data-via="openphoto">Tweet</a></div>
                  <div class="google"><g:plusone size="medium" href="http://www.openphoto.me"></g:plusone></div>-->
                </div>
              </div>
            </div>
          </div>
          <?php if($photos[0]['totalRows'] < 12 && $i >= 6) break; ?>
        <?php } ?>
      </div>
      <?php if(count($photos) > 1) { ?>
        <div class="carousel-control">
          <a class="right btn" href="#homeCarousel" data-slide="next"><i class="icon-chevron-right icon-large"></i></a>
          <a class="pause btn" href="#homeCarousel" data-slide="pause"><i class="icon-pause icon-large"></i></a>
          <a class="left btn" href="#homeCarousel" data-slide="prev"><i class="icon-chevron-left icon-large"></i></a>
        </div>
      <?php } ?>
    </div>

    <?php if($photos[0]['totalRows'] > 0) { ?>
      <!-- thumbnails -->
      <ul class="thumbnails carouselthumbs">
        <?php foreach($photos as $i => $photo) { ?>
          <li class="span2">
            <a href="<?php $this->url->photoView($photo['id']); ?>" class="thumbnail <?php if($i == 0) { ?>active<?php } ?>">
              <img src="<?php $this->url->photoUrl($photo, '870x652xCR'); ?>" alt="<?php $this->utility->safe($photo['title']); ?>" />
            </a>
          </li>
          <?php if($photos[0]['totalRows'] < 12 && $i >= 6) break; ?>
        <?php } ?>
      </ul>
    <?php } ?>
  <?php } ?>
<?php } else { ?>
  <div class="row">
    <?php $this->theme->display('partials/no-content.php', array('type' => 'upload')); ?>
  </div>
<?php } ?>
