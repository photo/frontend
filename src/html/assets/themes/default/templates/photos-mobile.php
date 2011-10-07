<div data-role="page" data-add-back-btn="true" id="Gallery1" class="gallery-page" data-theme="c">
	<div data-role="header" class="photoheader" data-theme="c"></div>
	<div data-role="content">	
		<ul class="gallery">
      <?php foreach($photos as $photo) { ?>
        <li><a href="<?php Url::photoUrl($photo, getConfig()->get('photoSizes')->detail); ?>" rel="external"><img src="<?php Url::photoUrl($photo, getConfig()->get('photoSizes')->thumbnail); ?>" alt="Image 001" /></a></li>
      <?php } ?>
		</ul>
	</div>
	
	<div data-role="footer" data-theme="c">
		<h4>The OpenPhoto Project &#169; 2011</h4>
	</div>
</div>

