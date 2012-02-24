<?php /*
<?php if(count($activities) > 0) { ?>
  <ul>
    <?php foreach($activities as $key => $activityGrp) { ?>
      <li>
        <?php if(preg_match('/photo-upload|photo-update|action-create/', $key)) { ?>
          <?php $this->theme->display(sprintf('partials/feed-%s.php', $activityGrp[0]['type']), array('activities' => $activityGrp)); ?>
        <?php } ?>
      </li>
    <?php } ?>
  </ul>
<?php } ?>
<?php */ ?>

<?php if($photos[0]['totalRows'] == 0) { ?>
  <?php if($this->user->isOwner()) { ?>
    <a href="<?php $this->url->photoUpload(); ?>" class="link" title="Start uploading now!"><img src="<?php $this->theme->asset('image', 'front.png'); ?>" class="front" /></a>
    <h1>Oh no, you haven't uploaded any photos yet. <a href="<?php $this->url->photoUpload(); ?>" class="link">Start Now</h1>
  <?php } else { ?>
    <img src="<?php $this->theme->asset('image', 'front-general.png'); ?>" class="front" />
    <h1>Sorry, no photos. <a class="login-click browserid link">Login</a> to upload some.</h1>
  <?php } ?>
<?php } else { ?>
	<section id="slideshow">
		<div class="flex-container">
			<div class="flexslider">
				 <ul class="slides">
					<?php foreach($photos as $photo) { ?>
					<li>
						<a href="<?php $this->url->photoView($photo['id']); ?>" title="Click to see detail"><img src="<?php $this->url->photoUrl($photo, '800x450xCR'); ?>" /></a>
						<?php if(isset($photo['title']) && !empty($photo['title'])) { ?>
							<p class="flex-caption"><?php $this->utility->safe($photo['title']); ?></p>
						<?php } ?>
					</li>
				  	<?php } ?>
				 </ul>
			</div>
		</div>
	</section>
<?php } ?>

