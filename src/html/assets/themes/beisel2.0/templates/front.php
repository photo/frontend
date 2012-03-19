<?php if(!empty($activities) && $photos[0]['totalRows'] > 0) { ?>
  <?php if(!empty($activities)) { ?>
    <!-- activity feed -->
    <div class="row activityfeed-startpage">
      <div class="span12">
        <div id="feedCarousel" class="carousel feed">
          <div class="span3">
            <h2>Latest activity</h2>
            <div class="carousel-control feed">
              <a class="left btn" href="#feedCarousel" data-slide="prev"><i class="icon-chevron-left icon-large"></i></a>
              <a class="right btn" href="#feedCarousel" data-slide="next"><i class="icon-chevron-right icon-large"></i></a>
            </div>
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
                <div class="map"><img src="<?php $this->utility->staticMapUrl($photo['latitude'], $photo['longitude'], 10, '250x100'); ?>"></div>
                <a href="" class="invert"><i class="icon-map-marker"></i> <?php printf('%s, %s', $this->utility->safe($photo['longitude'], false), $this->utility->safe($photo['latitude'], false)); ?></a><br/>
              <?php } ?>
              <!--<a href="" class="invert"><i class="icon-heart"></i> 4 favorites</a><br />-->
              <a href="" class="invert"><i class="icon-comment"></i> <?php if(!isset($photos['actions'])) { ?>0<?php } else { $this->utility->safe(count($photos['actions'])); } ?> comments &amp; favorites</a><br />
              <!--<a href="" class="invert"><i class="icon-signal"></i> 154 visits</a><br />
              <a href="" class="invert"><i class="icon-folder-close"></i> path photos</a>-->
              <br /><br />
              <?php if(count($photo['tags']) > 0) { ?>
                <p>
                  <?php foreach($photo['tags'] as $tag) { ?>
                    <span class="label label-tag">
                      <a href="<?php $this->url->photosView(sprintf('tags-%s', $this->utility->safe($tag, false))); ?>"><?php $this->utility->safe($tag); ?></a>
                    </span>
                  <?php } ?>
                </p>
              <?php } ?>
            </div>
          </div>
          <?php if($photos[0]['totalRows'] < 12 && $i >= 6) break; ?>
        <?php } ?>
      </div>
      <div class="carousel-control">
        <a class="right btn" href="#homeCarousel" data-slide="next"><i class="icon-chevron-right icon-large"></i></a>
        <a class="pause btn" href="#homeCarousel" data-slide="pause"><i class="icon-pause icon-large"></i></a>
        <a class="left btn" href="#homeCarousel" data-slide="prev"><i class="icon-chevron-left icon-large"></i></a>
      </div>
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
