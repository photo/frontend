<div class="front">
  <p>
    <?php if($photos[0]['totalRows'] == 0) { ?>
      <?php if(User::isOwner()) { ?>
        <h1>Oh no, you haven't uploaded any photos yet. <a href="<?php Url::photoUpload(); ?>" class="button">Start Now</a>
        <img src="<?php getTheme()->asset('image', 'front.jpg'); ?>" class="front">
      <?php } else { ?>
        <h1>Sorry, no photos. <a class="login-click button">Login</a> to upload some.</h1>
        <img src="<?php getTheme()->asset('image', 'front-general.jpg'); ?>" class="front">
      <?php } ?>
    <?php } else { ?>
      <div class="front-slideshow">
        <?php foreach($photos as $photo) { ?>
          <img src="<?php Url::photoUrl($photo, '800x450xCR'); ?>" data-origin="<?php Url::photoView($photo['id']); ?>">
        <?php } ?>
      </div>
    <?php } ?>
  </p>
</div>
