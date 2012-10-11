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
          <li class="photos"><a href="<?php $this->url->photosView(); ?>"><i class="icon-picture icon-large"></i> Gallery</a></li>
          <li class="albums"><a href="<?php $this->url->albumsView(); ?>"><i class="icon-th icon-large"></i> Albums</a></li>
          <li class="tags"><a href="<?php $this->url->tagsView(); ?>"><i class="icon-tags icon-large"></i> Tags</a></li>
          <?php if($this->user->isOwner()) { ?>
            <li class="upload"><a href="<?php $this->url->photosUpload(); ?>"><i class="icon-upload-alt icon-large"></i> Upload</a></li>
            <li class="manage">
              <ul class="nav pull-right">
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-cogs icon-large"></i> Manage <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php $this->url->managePhotos(); ?>"><i class="icon-picture icon-large"></i> Photos</a></li>
                    <li><a href="<?php $this->url->manageAlbums(); ?>"><i class="icon-th icon-large"></i> Albums</a></li>
                    <li><a href="<?php $this->url->manageGroups(); ?>"><i class="icon-group icon-large"></i> Groups</a></li>
                    <li><a href="<?php $this->url->manageApps(); ?>"><i class="icon-sitemap icon-large"></i> Applications</a></li>
                    <li><a href="<?php $this->url->manageSettings(); ?>"><i class="icon-wrench icon-large"></i> Settings</a></li>
                  </ul>
                </li>
              </ul>
            </li>
          <?php } ?>
        </ul>

        <ul class="nav pull-right">
          <li class="dropdown">
            <?php if($this->user->isLoggedIn()) { ?>
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?php $this->utility->safe($this->user->getAvatarFromEmail(25, $this->user->getEmailAddress())); ?>" class="gravatar"/> <b class="caret"></b></a>
            <?php } ?>
            <ul class="dropdown-menu">
              <?php if($this->user->isLoggedIn()) { ?>
                <li><a href="<?php $this->url->userLogout(); ?>"><i class="icon-signout icon-large"></i> Logout</a></li>
              <?php } ?>
            </ul>
          </li>
        </ul>
        <?php if(!$this->user->isLoggedIn()) { ?>
          <ul class="nav pull-right">
            <li><a href="https://openphoto.me/signup"><i class="icon-plus icon-large"></i> Create an account</a></li>
            <li><a href="#" class="login-modal-click"><i class="icon-signin icon-large"></i> Sign in</a></li>
          </ul>
        <?php } ?>
        <form class="navbar-search pull-right form-horizontal search-submit" action="<?php $this->utility->isActiveTab('manage') ? $this->url->managePhotos() : $this->url->photosView(); ?>">
          <div class="input-append">
            <select class="search-query span2 typeahead-tags tags-focus"  data-placeholder="Search by tags..." multiple id="navigation-search-input" name="tags"></select>
            <button class="btn add-on"><i class="icon-search icon-large"></i></button>
            <!--<input class="search-query span2 typeahead-tags tags-focus" autocomplete="off" id="navigation-search-input" name="tags" size="16" type="text">-->
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
