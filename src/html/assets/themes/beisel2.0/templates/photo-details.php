    <div class="subnav photodetail-nav">
      <ul class="nav nav-pills">
      <li><a href="#" class="<?php if($this->user->isLoggedIn()) { ?>action-post-click<?php } else { ?>login-modal-click<?php } ?>"><i class="icon-heart icon-large"></i> Mark as favorite</a></li>
        <li><a href="#comment-form"><i class="icon-comment icon-large"></i> Leave a comment</a></li>
        <!-- <li><a href="#"><i class="icon-envelope icon-large"></i> Share this photo</a></li> -->
        <?php if(isset($photo['next'])) { ?>
          <li class="last"><a href="<?php $this->url->photoView($photo['next']['id'], $options); ?>">Next <i class="icon-arrow-right icon-large"></i></a></li>
        <?php } ?>
        <?php if(isset($photo['previous'])) { ?>
          <li class="last"><a href="<?php $this->url->photoView($photo['previous']['id'], $options); ?>"><i class="icon-arrow-left icon-large"></i> Previous</a></li>
        <?php } ?>
      </ul>
    </div>
  
    <div class="row hero-unit photodetail photo-view">
      <?php if(isset($_GET['modal']) && $_GET['modal'] == 'true') { ?>
        <div class="modal-header">
          <a href="#" class="close" data-dismiss="modal">&times;</a>
          <h3>
            <?php if($photo['title'] != '') { ?>
              <?php $this->utility->safe($photo['title']); ?>
            <?php } else { ?>
              <?php $this->utility->safe($photo['filenameOriginal']); ?>
            <?php } ?>
          </h3>
        </div>
      <?php  } ?>
      <div class="span9">
        <div class="photodetailpos">
          <?php if($photo['permission'] == 0) { ?>
            <div class="private" title="private"><i class="icon-lock icon-large"></i></div>
          <?php } ?>
          <img class="photo" src="<?php $this->url->photoUrl($photo, $this->config->photoSizes->detail); ?>" alt="<?php $this->utility->safe($photo['title']); ?>">
        </div>
        <div class="comment-form">
          <a name="comment-form"></a>
          <form class="form-horizontal" method="post" action="<?php $this->url->actionCreate($photo['id'], 'photo'); ?>">
            <?php if(count($photo['actions']) > 0) { ?>
              <?php foreach($photo['actions'] as $action) { ?>
                <div class="row">
                  <div class="span1">
                    <img src="<?php $this->utility->safe($this->user->getAvatarFromEmail(70, $action['email'])); ?>" class="avatar">
                  </div>
                  <div class="span8">
                    <strong><?php $this->utility->getEmailHandle($action['email']); ?></strong> <em>(<?php $this->utility->safe($this->utility->dateLong($action['datePosted'])); ?>)</em><br/>
                    <?php if($action['type'] == 'comment') { ?>
                      <i class="icon-heart orange"></i> <?php $this->utility->safe($action['value']); ?>
                    <?php } else { ?>
                      <i class="icon-heart orange"></i> Marked this photo as a favorite.
                    <?php } ?>
                  </div>
                </div>
                <hr />
              <?php } ?>
            <?php } ?>
            <fieldset>
              <div class="control-group">
                <label class="control-label" for="textarea">Your comment</label>
                <div class="controls">
                  <textarea rows="6" class="input-xlarge" name="value" class="comment" <?php if(!$this->user->isLoggedIn()) { ?>disabled="true"<?php } ?> ></textarea>
                  <input type="hidden" name="type" value="comment">
                  <input type="hidden" name="targetUrl" value="<?php $this->utility->safe(sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'])); ?>">
                  <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
                </div>
              </div>
              <div class="form-actions">
                <?php if($this->user->isLoggedIn()) { ?>
                  <button type="submit" class="btn btn-primary"><i class="icon-comment"></i> Leave a comment</button>
                  <!--<button type="submit" class="btn btn-secondary"><i class="icon-heart"></i> Mark as favorite</button>-->
                <?php } else { ?>
                  <button type="button" class="btn btn-primary login-modal-click"><i class="icon-signin"></i> Sign in to comment</button>
                <?php } ?>
              </div>
            </fieldset>
          </form>
          <form method="post" action="<?php $this->url->actionCreate($photo['id'], 'photo'); ?>" id="favorite-form" class="hidden">
            <input type="hidden" name="type" value="favorite">
            <input type="hidden" name="targetUrl" value="<?php $this->utility->safe(sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'])); ?>">
            <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
          </form>
        </div>
      </div>
      <div class="span3">
        <div class="morephotos">
          <strong>Discover more photos:</strong>
          <ul>
            <?php if(!empty($photo['previous'])) { ?>
              <li class="buttonmp"><a href="<?php $this->url->photoView($photo['previous'][0]['id'], $options); ?>"><button type="button" class="btn btn-primary photo-view-click previous-photo"><i class="icon-arrow-left"></i></button></a></li>
              <li class="span1 mspacer"><a href="<?php $this->url->photoView($photo['previous'][0]['id'], $options); ?>"><img src="<?php $this->url->photoUrl($photo['previous'][0], $this->config->photoSizes->nextPrevious); ?>" alt="Previous photo: <?php $this->utility->safe($photo['previous']['title']); ?>" class="photo-view-click previous-photo" data-id="<?php $this->utility->safe($photo['previous']['id']); ?>" /></a></li>
            <?php } else { ?>
              <li class="buttonmp"><button type="button" class="btn disabled"><i class="icon-arrow-left"></i></button></li>
              <li class="span1 mspacer">&nbsp;</li>
            <?php } ?>
            <?php if(!empty($photo['next'])) { ?>
              <li class="span1"><a href="<?php $this->url->photoView($photo['next'][0]['id'], $options); ?>"><img src="<?php $this->url->photoUrl($photo['next'][0], $this->config->photoSizes->nextPrevious); ?>" alt="Next photo: <?php $this->utility->safe($photo['previous']['title']); ?>" class="photo-view-click next-photo" data-id="<?php $this->utility->safe($photo['next']['id']); ?>" /></a></li>
              <li class="buttonmp"><a href="<?php $this->url->photoView($photo['next'][0]['id'], $options); ?>"><button type="button" class="btn btn-primary photo-view-click next-photo"><i class="icon-arrow-right"></i></button></a></li>
            <?php } else { ?>
              <li class="span1">&nbsp;</li>
              <li class="buttonmp"><button type="button" class="btn disabled"><i class="icon-arrow-right"></i></button></li>
            <?php } ?>
          </ul>
          <hr />
        </div>
        <h1>
          <?php if($photo['title'] != '') { ?>
            <?php $this->utility->safe($photo['title']); ?>
          <?php } else { ?>
            <?php $this->utility->safe($photo['filenameOriginal']); ?>
          <?php } ?>
        </h1>
        <?php if($photo['description'] != '') { ?>
          <p><?php $this->utility->safe($photo['description']); ?></p>
        <?php } ?>
        <p>This photo was taken <?php $this->utility->timeAsText($photo['dateTaken']); ?></p>
        <div class="social">
          <div class="facebook">
            <?php $this->plugin->invoke('renderPhotoDetail', $photo); ?>
          </div>
        </div>
        <?php if(isset($photo['latitude']) && !empty($photo['latitude'])) { ?>
          <div class="map">
            <a href="<?php $this->utility->mapLinkUrl($photo['latitude'], $photo['longitude'], 5); ?>"><i class="icon-map-marker"></i>	<?php $this->utility->safe($photo['latitude']); ?>, <?php $this->utility->safe($photo['longitude']); ?>
            <img src="<?php $this->utility->staticMapUrl($photo['latitude'], $photo['longitude'], 5, '250x100'); ?>"></a>
          </div>
        <?php } ?>
        <div class="iconbox">
          <a href="#" class="invert"><i class="icon-comment"></i> <?php echo count($photo['actions']); ?> comments &amp; favorites</a>
          <a href="#" class="invert"><i class="icon-eye-open"></i> <?php $this->utility->licenseName($photo['license']); ?></a>
          <?php if($this->user->isOwner() || $this->config->site->allowOriginalDownload == 1) { ?>
            <a href="<?php $this->url->photoDownload($photo); ?>" class="invert"><i class="icon-download"></i> Download original</a>
          <?php } ?>
          <?php if($this->user->isOwner()) { ?>
            <a href="#" class="photo-edit-click invert" data-id="<?php $this->utility->safe($photo['id']); ?>"><i class="icon-edit"></i> Edit details</a>
          <?php } ?>
        </div>
        <?php if(count($photo['tags']) > 0) { ?>
          <div class="tags">
            <?php foreach($photo['tags'] as $tag) { ?>
              <a href="<?php $this->url->photosView("tags-{$tag}"); ?>" class="label label-tag"><?php $this->utility->safe($tag); ?></a>
            <?php } ?>
          </div>
        <?php } ?>
        <?php if(!empty($photo['exifCameraMake']) && !empty($photo['exifCameraMake'])) { ?>
          <ul class="camera">
            <li><i class="icon-camera orange"></i></li>
            <li>
              <?php foreach(array('exifCameraMake' => '<strong>Camera make:</strong> %s<br>',
                'exifCameraModel' => '<strong>Camera model:</strong> %s<br>',
                'exifFNumber' => '<strong>Av:</strong> f/%1.1F<br>',
                'exifExposureTime' => '<strong>Tv:</strong> %s<br>',
                'exifISOSpeed' => '<strong>ISO:</strong> %d<br>',
                'exifFocalLength' => '<strong>Focal Length:</strong> %1.0fmm') as $key => $value) { ?>
                  <?php if(!empty($photo[$key])) { ?>
                  <?php printf($value, $this->utility->safe($photo[$key], false)); ?>
                  <?php } ?>
                <?php } ?>
            </li>
          </ul>
        <?php } ?>
      </div>
    </div>
