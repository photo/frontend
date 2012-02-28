<ul>
	<li id="nav-photos" <?php if($this->utility->isActiveTab('photos')) { ?> class="active" <?php } ?>>
		<a href="<?php $this->url->photosView(); ?>">Photos</a>
	</li>
	<li id="nav-tags" <?php if($this->utility->isActiveTab('tags')) { ?> class="active" <?php } ?>>
		<a href="<?php $this->url->tagsView(); ?>">Tags</a>
	</li>
	<li id="nav-tags" <?php if($this->utility->isActiveTab('albums')) { ?> class="active" <?php } ?>>
		<a href="<?php $this->url->albumsView(); ?>">Albums</a>
	</li>
<?php if($this->user->isOwner()) { ?>
	<li id="nav-upload" <?php if($this->utility->isActiveTab('upload')) { ?> class="active" <?php } ?>>
		<a href="<?php $this->url->photosUpload(); ?>">Upload</a>
	</li>
<?php } ?>
<?php if($this->user->isLoggedIn()) { ?>
	<li id="nav-signin">
		<img src="<?php echo $this->user->getAvatarFromEmail(40, $this->session->get('email')); ?>" id="avatar" class="settings-click" />
		<div class="settings-click">
			<?php echo $this->session->get('email'); ?>
			<ul id="settingsbar">
				<li id="nav-settings"><a href="<?php $this->url->userSettings(); ?>">Settings</a></li>
				<li id="nav-logout"><a href="<?php $this->url->userLogout(); ?>">Logout</a></li>
			<ul>
		</div>
	</li>
<?php } else { ?>
	<li id="nav-browserid">
    <?php if($this->plugin->isActive('FacebookConnect')) { ?>
      <a class="login-click facebook" title="Signin using Facebook"><img src="<?php $this->theme->asset('image', 'facebook-icon.gif'); ?>" class="login-click facebook" hspace="5" /></a>
    <?php } ?>
    <a class="login-click browserid" title="Signin using BrowserID"><img src="<?php $this->theme->asset('image', 'browserid.png'); ?>" class="login-click browserid" /></a>
	</li>
<?php } ?>
	<li id="nav-search" <?php if($this->utility->isActiveTab('search')) { ?> class="active" <?php } ?>>
		<form action="<?php $this->url->photosView(); ?>" method="get" id="form-tag-search">
			<input type="text" name="tags" placeholder="Enter a tag" class="select tags-autocomplete" /><button type="submit" class="search-click" title="Search now">Go</button>
		</form>
	</li>
</ul>
