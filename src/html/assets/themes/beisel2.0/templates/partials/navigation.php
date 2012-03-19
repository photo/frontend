<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="btn btn-navbar pull-left" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="brand" href="/" title="The OpenPhoto Project">The OpenPhoto Project</a>
      <div class="nav-collapse">
        <ul class="nav">
          <li <?php if($this->utility->isActiveTab('photos')) { ?>class="active"<?php } ?>><a href="<?php $this->url->photosView(); ?>"><i class="icon-picture icon-large"></i> Gallery</a></li>
          <li <?php if($this->utility->isActiveTab('tags')) { ?>class="active"<?php } ?>><a href="<?php $this->url->tagsView(); ?>"><i class="icon-tag icon-large"></i> Tags</a></li>
          <?php if($this->user->isOwner()) { ?>
            <li <?php if($this->utility->isActiveTab('upload')) { ?>class="active"<?php } ?>><a href="<?php $this->url->photosUpload(); ?>"><i class="icon-upload icon-large"></i> Upload</a></li>
            <li <?php if($this->utility->isActiveTab('manage')) { ?>class="active"<?php } ?>><a href="<?php $this->url->manage(); ?>"><i class="icon-th icon-large"></i> Manage</a></li>
          <?php } ?>
        </ul>

        <ul class="nav pull-right">
          <li class="dropdown">
            <?php if($this->user->isLoggedIn()) { ?>
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?php $this->utility->safe($this->user->getAvatarFromEmail(25, $this->user->getEmailAddress())); ?>" class="gravatar"/> <b class="caret"></b></a>
            <?php } ?>
            <ul class="dropdown-menu">
              <?php if($this->user->isLoggedIn()) { ?>
                <?php if($this->user->isOwner()) { ?>
                  <li><a href="<?php $this->url->userSettings(); ?>"><i class="icon-cog icon-large"></i> Preferences</a></li>
                  <li class="divider"></li>
                <?php } ?>
                <li><a href="<?php $this->url->userLogout(); ?>"><i class="icon-signout icon-large"></i> Logout</a></li>
              <?php } ?>
            </ul>
          </li>
        </ul>
        <?php if(!$this->user->isLoggedIn()) { ?>
          <ul class="nav pull-right">
            <li><a href="https://openphoto.me/signup"><i class="icon-plus icon-large"></i> Register</a></li>
            <li><a href="#" class="login-modal-click"><i class="icon-signin icon-large"></i> Sign in</a></li>
          </ul>
        <?php } ?>
        <form class="navbar-search pull-right form-horizontal" action="<?php $this->url->photosView(); ?>">
          <div class="input-append">
            <input class="search-query span2" id="appendedInput" name="tags" size="16" type="text">
            <a href="#" class="add-on search-click"><i class="icon-search icon-large"></i></a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
