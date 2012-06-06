<div class="front">
  <p>
    <?php if($photos[0]['totalRows'] == 0) { ?>
      <?php if($this->user->isOwner()) { ?>
        <h1>Oh no, you haven't uploaded any photos yet. <a href="<?php $this->url->photoUpload(); ?>" class="button">Start Now</a>
        <img src="<?php $this->theme->asset('image', 'front.jpg'); ?>" class="front">
      <?php } else { ?>
        <h1>Sorry, no photos. <a class="login-click browserid button">Login</a> to upload some.</h1>
        <img src="<?php $this->theme->asset('image', 'front-general.jpg'); ?>" class="front">
      <?php } ?>
    <?php } else { ?>
      <div class="front-slideshow">
        <?php foreach($photos as $photo) { ?>
          <img src="<?php $this->url->photoUrl($photo, '800x450xCR'); ?>" data-origin="<?php $this->url->photoView($photo['id']); ?>">
        <?php } ?>
      </div>
    <?php } ?>
  </p>
</div>
