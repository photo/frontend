<?php if(count($successPhotos) > 0) { ?>
  <?php if(count($failure) === 0) { ?>
    <h2>Your photos are finished uploading! Now what?</h2>
  <?php } else { ?>
  <h2>Your photos are finished uploading, but <?php echo count($failure); ?> had problems. Now what?</h2>
  <?php } ?>

  <p>
    Post your photos to <?php if($facebookId) { ?>Facebook or <?php } ?>Twitter.
    <ul class="unstyled upload-share">
      <li>
        <a href="https://twitter.com/intent/tweet?original_referer=<?php $this->utility->getAbsoluteUrl(''); ?>&text=<?php echo urlencode('I just uploaded some photos on @OpenPhoto.'); ?>&url=<?php echo urlencode($this->utility->getAbsoluteUrl($url, false)); ?>" class="btn btn-theme-secondary sharePopup" data-width="575" data-height="400"><i class="icon-twitter icon-large"></i> Share on Twitter</a>
      </li>
      <?php if($facebookId) { ?>
        <li>
          <a href="https://www.facebook.com/dialog/feed?app_id=<?php $this->utility->safe($facebookId); ?>&link=<?php $this->utility->getAbsoluteUrl($url); ?>&picture=<?php $this->utility->safe($successPhotos[0][$this->config->photoSizes->thumbnail]); ?>&name=My+photos+on+OpenPhoto&description=I+uploaded+some+photos+on+OpenPhoto.&display=popup" class="btn  btn-theme-secondary share-facebook-click"
             data-link="<?php $this->utility->getAbsoluteUrl($url); ?>" data-picture="<?php $this->utility->safe($successPhotos[0][$this->config->photoSizes->thumbnail]); ?>" data-name="My photos on OpenPhoto" data-description="I uploaded some photos on OpenPhoto." data-display=popup" data-width="450" data-height="300"><i class="icon-facebook icon-large"></i> Share on Facebook</a>
        </li>
      <?php } ?>
    </ul>
  </p>

  <p>
    Or simply go <a href="<?php $this->url->photosView(sprintf('ids-%s', $successIds)); ?>">look at your photos</a>.
  </p>
<?php } else { // no photos uploaded ?>
  <h2>None of your photos were uploaded</h2>
  <p>
    See below for more details. If you continue to have problems drop us a note on our mailing list <a href="mailto:support@trovebox.com">support@trovebox.com</a>.
  </p>
<?php } ?>

<?php if(count($successPhotos) > 0) { ?>
  <h3>Here's a breakdown of your upload</h3>
  <strong><span class="label label-success"><?php printf('%d %s %s', count($successPhotos), $this->utility->plural(count($successPhotos), 'photo', false), $this->utility->selectPlural(count($successPhotos), 'was', 'were', false)); ?> uploaded successfully.</span></strong>
  <div class="upload-preview success photo-grid">
    <div class="photo-grid-hr"></div>
  </div>
  <hr>
<?php } ?>

<?php if(count($duplicatePhotos) > 0) { ?>
  <strong><span class="label label-warning"><?php echo count($duplicatePhotos); ?> of them already existed in your account.</span></strong>
  <div class="upload-preview">
    <ul class="thumbnails duplicates">
      <?php foreach($duplicatePhotos as $photo) { ?>
        <li><a href="<?php $this->utility->safe($photo['url']); ?>" class="thumbnail"><img src="<?php $this->utility->safe($photo[$this->config->photoSizes->thumbnail]); ?>"></a></li>
      <?php } ?>
    </ul>
  </div>
  <hr>
<?php } ?>

<?php if(count($failure) > 0) { ?>
  <strong><span class="label label-important"><?php printf('%d %s', count($failure), $this->utility->plural(count($failure), 'photo', false)); ?> could not be uploaded. Booo!</span></strong>
  <div>
    <ul>
      <?php foreach($failure as $name) { ?>
        <li><?php $this->utility->safe($name); ?></li>
      <?php } ?>
    </ul>
  </div>
<?php } ?>
