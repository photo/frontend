<div class="headerbar">
	Photos <!-- function bar for e.g. sharing function -->
</div>

<section class="photo-column">
	<h1><?php Utility::safe($photo['title']); ?></h1>
	<img class="photo" width="<?php Utility::safe($photo['thisWidth']); ?>" height="<?php Utility::safe($photo['thisHeight']); ?>" src="<?php Url::photoUrl($photo, getConfig()->get('photoSizes')->detail); ?>" alt="<?php Utility::safe($photo['title']); ?>">
	<p class="description"><?php Utility::safe($photo['description']); ?></p>
	<?php if(count($photo['actions']) > 0) { ?>
	  <ul class="comments" id="comments">
	    <?php foreach($photo['actions'] as $action) { ?>
	      <li class="action-container-<?php echo $action['id']; ?>">
	        <img src="<?php echo User::getAvatarFromEmail(40, $action['email']); ?>" class="avatar">
			<div>
				<strong><?php echo getSession()->get('email'); ?> <small>(<?php echo Utility::dateLong($action['datePosted']); ?>)</small></strong>
		        <?php if($action['type'] == 'comment') { ?>
		          <span><?php echo $action['value']; ?></span>
		        <?php } else { ?>
		          <span>Marked this photo as a favorite.</span>
		        <?php } ?>
		        <span class="date">
		          <?php if(User::isOwner()) { ?>
		            <form method="post" action="<?php Url::actionDelete($action['id']); ?>">
		              <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
		              <a href="<?php Url::actionDelete($action['id']); ?>" class="action-delete-click"><span></span>Delete comment</a>
		            </form>
		          <?php } ?>
		        </span>
			</div>
	      </li>
	    <?php } ?>
	  </ul>
	<?php } ?>
	<div class="comment-form">
	  <form method="post" action="<?php Url::actionCreate($photo['id'], 'photo'); ?>">
	    <textarea rows="5" cols="50" name="value" class="comment" <?php if(!User::isLoggedIn()) { ?>disabled="true"<?php } ?> ></textarea>
	    <input type="hidden" name="type" value="comment">
	    <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
	    <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
	    <div class="buttons">
	    <?php if(User::isLoggedIn()) { ?>
	      <button type="submit">Leave a comment</button>
	    <?php } else { ?>
	      <button type="button" class="login-click">Sign in to comment</button>
	    <?php } ?>
	    </div>
	  </form>
	  <?php if(User::isLoggedIn()) { ?>
	    <form method="post" action="<?php Url::actionCreate($photo['id'], 'photo'); ?>">
	      <input type="hidden" name="value" value="1">
	      <input type="hidden" name="type" value="favorite">
	      <input type="hidden" name="targetUrl" value="<?php sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']); ?>">
	      <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
	      <button type="submit">Favorite</button>
	    </form>
	  <?php } ?>
	</div>
	
</section>
<aside class="sidebar">
	
	<p><strong>Discover more photos</strong></p>
	<ul class="image-pagination">
		<?php if(!empty($photo['previous'])) { ?>
		<li class="previous">
			<a href="<?php Url::photoView($photo['previous']['id'], $options); ?>" title="Go to previous photo">
				<img src="<?php Url::photoUrl($photo['previous'], getConfig()->get('photoSizes')->nextPrevious); ?>" alt="Go to previous photo" />
				<span class="prev"><span></span></span>
				<span class="audible">Go to previous photo</span>
			</a>
		</li>
		<?php } else { ?>
			<li class="previous empty">
				<img src="<?php getTheme()->asset('image', 'empty.png'); ?>" alt="No photo" />
			</li>
		<?php } ?>
		<?php if(!empty($photo['next'])) { ?>
		<li class="next">
			<a href="<?php Url::photoView($photo['next']['id'], $options); ?>" title="Go to next photo">
				<img src="<?php Url::photoUrl($photo['next'], getConfig()->get('photoSizes')->nextPrevious); ?>" alt="Go to next photo" />
				<span class="next"><span></span></span>
				<span class="audible">Go to next photo</span>
			</a>
		</li>
		<?php } else { ?>
			<li class="next empty">
				<img src="<?php getTheme()->asset('image', 'empty.png'); ?>" alt="No photo" />
			</li>
		<?php } ?>
	</ul>

	<p><strong>Photo details</strong></p>
	<ul class="meta">
		<li class="date"><span></span><?php Utility::dateLong($photo['dateTaken']); ?></li>
		<li class="heart"><span></span><strong><?php echo count($photo['actions']); ?></strong> <a href="#comments" class="action-jump-click" title="Jump to favorites &amp; comments">favorites &amp; comments</a></li>
		<li class="tags"><span></span><?php Url::tagsAsLinks($photo['tags']); ?></li>
		<?php if(isset($photo['license'])) { ?>
		<li class="license"><span></span><?php Utility::licenseLong($photo['license']); ?></li>
		<?php } ?>
		<?php if(!empty($photo['latitude']) && !empty($photo['latitude'])) { ?>
		<li class="location">
			<span></span>
			<?php Utility::safe($photo['latitude']); ?>, <?php Utility::safe($photo['longitude']); ?>
			<img src="<?php Utility::staticMapUrl($photo['latitude'], $photo['longitude'], 14, '255x150'); ?>" class="map">
		</li>
		<?php } ?>
		<?php if(!empty($photo['exifCameraMake']) && !empty($photo['exifCameraMake'])) { ?>
		<li class="exif">
			<span></span>
			<ul>
				<?php foreach(array('exifCameraMake' => 'Camera make: <em>%s</em>',
				'exifCameraModel' => 'Camera model: <em>%s</em>',
				'exifFNumber' => 'Av: <em>f/%1.0F</em>',
				'exifExposureTime' => 'Tv: <em>%s</em>',
				'exifISOSpeed' => 'ISO: <em>%d</em>',
				'exifFocalLength' => 'Focal Length: %1.0fmm') as $key => $value) { ?>
					<?php if(!empty($photo[$key])) { ?>
					<li><?php printf($value, Utility::safe($photo[$key], false)); ?></li>
					<?php } ?>
				<?php } ?>
			</ul>
		</li>
		<?php } ?>
		<?php if(User::isOwner()) { ?>
		<li class="edit">
			<span></span>
			<a href="<?php Url::photoEdit($photo['id']); ?>" class="button photo-edit-click">Edit this photo</a>
		</li>
		<?php } ?>
	</ul>
</aside>
<div style="clear:both;"></div>