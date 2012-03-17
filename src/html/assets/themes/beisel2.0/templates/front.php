      <?php if(count($activities) > 0) { ?>
        <!-- activity feed -->
        <div class="row activityfeed-startpage">
          <div class="span12">
            <div id="feedCarousel" class="carousel feed">
              <div class="span3">
                <h2>Latest activity</h2>
                <div class="carousel-control feed">
                  <a class="right btn" href="#feedCarousel" data-slide="next"><i class="icon-chevron-right icon-large"></i></a>
                  <a class="left btn" href="#feedCarousel" data-slide="prev"><i class="icon-chevron-left icon-large"></i></a>
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
        <?php if($this->user->isOwner()) { ?>
          <a href="<?php $this->url->photoUpload(); ?>" class="link" title="Start uploading now!"><img src="<?php $this->theme->asset('image', 'front.png'); ?>" class="front" /></a>
          <h1>Oh no, you haven't uploaded any photos yet. <a href="<?php $this->url->photoUpload(); ?>" class="link">Start Now</h1>
        <?php } else { ?>
          <img src="<?php $this->theme->asset('image', 'front-general.png'); ?>" class="front" />
          <h1>Sorry, no photos. <a class="login-click browserid link">Login</a> to upload some.</h1>
        <?php } ?>
      <?php } else { ?>
        <!-- carousel -->
        <div id="homeCarousel" class="row hero-unit carousel front">
          <!-- large photo -->
          <div class="carousel-inner">
            <?php for($i=0; $i<5 && $i<$photos[0]['totalRows']; $i++) { ?>
              <div class="item <?php if($i == 0) { ?>active<?php } ?> row">
                <a href="<?php $this->url->photoView($photos[$i]['id']); ?>" class="span9"><img src="<?php $this->url->photoUrl($photos[$i], '870x652xCR'); ?>" alt="<?php $this->utility->safe($photos[$i]['title']); ?>" /></a>
                <div class="span3">
                  <h1>
                    <a href="<?php $this->url->photoView($photos[$i]['id']); ?>">
                      <?php if(empty($photos[$i]['title'])) { ?>
                        <?php $this->utility->safe($photos[$i]['filenameOriginal']); ?>
                      <?php } else { ?>
                        <?php $this->utility->safe($photos[$i]['title']); ?>
                      <?php } ?>
                    </a>
                  </h1>
                  <p>This photo was taken <?php $this->utility->safe($this->utility->timeAsText($photos[$i]['dateTaken'])); ?></p>
                  <?php if(isset($photos[$i]['longitude']) && !empty($photos[$i]['longitude'])) { ?>
                    <div class="map"><img src="<?php $this->utility->staticMapUrl($photos[$i]['latitude'], $photos[$i]['longitude'], 10, '250x100'); ?>"></div>
                    <a href="" class="invert"><i class="icon-map-marker"></i> <?php printf('%s, %s', $this->utility->safe($photos[$i]['longitude'], false), $this->utility->safe($photos[$i]['latitude'], false)); ?></a><br/>
                  <?php } ?>
                  <!--<a href="" class="invert"><i class="icon-heart"></i> 4 favorites</a><br />-->
                  <a href="" class="invert"><i class="icon-comment"></i> <?php if(!isset($photos['actions'])) { ?>0<?php } else { $this->utility->safe(count($photos['actions'])); } ?> comments &amp; favorites</a><br />
                  <!--<a href="" class="invert"><i class="icon-signal"></i> 154 visits</a><br />
                  <a href="" class="invert"><i class="icon-folder-close"></i> path photos</a>-->
                  <br /><br />
                  <?php if(count($photos[$i]['tags']) > 0) { ?>
                    <p>
                      <?php foreach($photos[$i]['tags'] as $tag) { ?>
                        <span class="label label-tag">
                          <a href="<?php $this->url->photosView(sprintf('tags-%s', $this->utility->safe($tag, false))); ?>"><?php $this->utility->safe($tag); ?></a>
                        </span>
                      <?php } ?>
                    </p>
                  <?php } ?>
                </div>
              </div>
            <?php } ?>
          </div>
          <div class="carousel-control">
            <a class="right btn" href="#homeCarousel" data-slide="next"><i class="icon-chevron-right icon-large"></i></a>
            <a class="pause btn" href="#homeCarousel" data-slide="pause"><i class="icon-pause icon-large"></i></a>
            <a class="left btn" href="#homeCarousel" data-slide="prev"><i class="icon-chevron-left icon-large"></i></a>
          </div>
          </div>
        </div>

        <?php if($photos[0]['totalRows'] > 5) { ?>
          <!-- thumbnails -->
          <ul class="thumbnails carouselthumbs">
            <?php for($i=5; $i<11 && $i<$photos[0]['totalRows']; $i++) { ?>
              <li class="span2">
                <a href="<?php $this->url->photoView($photos[$i]['id']); ?>" class="thumbnail <?php if($i == 0) { ?>active<?php } ?>">
                  <img src="<?php $this->url->photoUrl($photos[$i], '870x652xCR'); ?>" alt="<?php $this->utility->safe($photos[$i]['title']); ?>" />
                </a>
              </li>
            <?php } ?>
          </ul>
        <?php } ?>
      <?php } ?>
