<div class="front">
  <p>
    <?php if(empty($photos)) { ?>
      <?php if(User::isOwner()) { ?>
        <h1>Oh no, you haven't uploaded any photos yet. <a href="/photos/upload" class="button">Start Now</a>
        <img src="<?php getTheme()->asset('image', 'front.jpg'); ?>" class="front">
      <?php } else { ?>
        <h1>Sorry, no photos. <a class="login-click button">Login</a> to upload some.</h1>
        <img src="<?php getTheme()->asset('image', 'front-general.jpg'); ?>" class="front">
      <?php } ?>
    <?php } else { ?>
      <div class="front-slideshow">
        <?php foreach($photos as $photo) { ?>
          <img src="<?php Utility::photoUrl($photo, '800x450xCR'); ?>" data-origin="/photo/<?php Utility::safe($photo['id']); ?>">
        <?php } ?>
      </div>
    <?php } ?>
  </p>
</div>
