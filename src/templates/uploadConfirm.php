<?php if(count($successPhotos) > 0) { ?>
  <h2>What would you like to do next?</h2>

  <p>
    Post your photos to <!--Facebook or--> Twitter.
    <ul class="unstyled upload-share">
      <!--<li><a href="<?php $this->url->photosView(sprintf('ids-%s', $successIds)); ?>" class="btn btn-primary"><i class="icon-facebook icon-large"></i> Share on Facebook</a></li>-->
      <li><a href="http://twitter.com/home?status=<?php echo urlencode(sprintf('I just uploaded some photos at @OpenPhoto. %s', $this->utility->getAbsoluteUrl($this->url->photosView(sprintf('ids-%s', $successIds), false), false))); ?>" class="btn btn-primary popup-click" data-width="575" data-height="400"><i class="icon-twitter icon-large"></i> Share on Twitter</a></li>
    </ul>
  </p>

  <p>
    Or simply go <a href="<?php $this->url->photosView(sprintf('ids-%s', $successIds)); ?>">look at your photos</a>.
  </p>
<?php } else { // no photos uploaded ?>
  <h2>None of your photos were uploaded</h2>
  <p>
    See below for more details. If you continue to have problems drop us a note on our mailing list <a href="mailto:openphoto@googlegroups.com">openphoto@googlegroups.com</a>.
  </p>
<?php } ?>

<?php if(count($successPhotos) > 0) { ?>
  <h3>Here's a breakdown of your upload</h3>
  <strong><span class="label label-success"><?php printf('%d %s', count($successPhotos), $this->utility->plural(count($successPhotos), 'photo', false)); ?> were uploaded successfully.</span></strong>
  <div class="upload-preview">
    <ul class="thumbnails">
      <?php foreach($successPhotos as $photo) { ?>
        <li><a href="<?php $this->utility->safe($photo['url']); ?>" class="thumbnail"><img src="<?php $this->utility->safe($photo['path100x100xCR']); ?>"></a></li>
      <?php } ?>
    </ul>
  </div>
  <hr>
<?php } ?>

<?php if(count($duplicatePhotos) > 0) { ?>
  <strong><span class="label label-warning"><?php echo count($duplicatePhotos); ?> of them already existed in your account.</span></strong>
  <div class="upload-preview">
    <ul class="thumbnails duplicates">
      <?php foreach($duplicatePhotos as $photo) { ?>
        <li><a href="<?php $this->utility->safe($photo['url']); ?>" class="thumbnail"><img src="<?php $this->utility->safe($photo['path100x100xCR']); ?>"></a></li>
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
