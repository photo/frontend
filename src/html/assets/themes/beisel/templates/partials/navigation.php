<ul>
	<li id="nav-photos" <?php if(Utility::isActiveTab('photos')) { ?> class="active" <?php } ?>>
		<a href="<?php Url::photosView(); ?>">Photos</a>
	</li>
	<li id="nav-tags" <?php if(Utility::isActiveTab('tags')) { ?> class="active" <?php } ?>>
		<a href="<?php Url::tagsView(); ?>">Tags</a>
	</li>
<?php if(User::isOwner()) { ?>
	<li id="nav-upload" <?php if(Utility::isActiveTab('upload')) { ?> class="active" <?php } ?>>
		<a href="<?php Url::photosUpload(); ?>">Upload</a>
	</li>
<?php } ?>
<?php if(User::isLoggedIn()) { ?>
	<li id="nav-signin">
		<img src="<?php echo User::getAvatarFromEmail(40, getSession()->get('email')); ?>" id="avatar" class="settings-click" />
		<div class="settings-click">
			<?php echo getSession()->get('email'); ?>
			<ul id="settingsbar">
				<li id="nav-settings"><a href="<?php Url::userSettings(); ?>">Settings</a></li>
				<li id="nav-logout"><a href="<?php Url::userLogout(); ?>">Logout</a></li>
			<ul>
		</div>
	</li>
<?php } else { ?>
	<li id="nav-browserid">
    <?php if(getPlugin()->isActive('FacebookConnect')) { ?>
      <a class="login-click facebook" title="Signin using Facebook"><img src="<?php getTheme()->asset('image', 'facebook-icon.gif'); ?>" class="login-click facebook" hspace="5" /></a>
    <?php } ?>
    <a class="login-click browserid" title="Signin using BrowserID"><img src="<?php getTheme()->asset('image', 'browserid.png'); ?>" class="login-click browserid" /></a>
	</li>
<?php } ?>
	<li id="nav-search" <?php if(Utility::isActiveTab('search')) { ?> class="active" <?php } ?>>
		<form action="<?php Url::photosView(); ?>" method="get" id="form-tag-search">
			<input type="text" name="tags" placeholder="Enter a tag" class="select tags-autocomplete" /><button type="submit" class="search-click" title="Search now">Go</button>
		</form>
	</li>
</ul>
