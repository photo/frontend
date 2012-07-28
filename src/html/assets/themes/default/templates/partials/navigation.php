<ul>
  <li id="nav-photos" <?php if($this->utility->isActiveTab('photos')) { ?> class="on" <?php } ?>>
    <a href="<?php $this->url->photosView(); ?>"><i class="icon-picture"></i>Photos</a>
  </li>
  <li id="nav-albums" <?php if($this->utility->isActiveTab('albums')) { ?> class="on" <?php } ?>>
    <a href="<?php $this->url->albumsView(); ?>"><i class="icon-th"></i>Albums</a></li>
  </li>
  <li id="nav-tags" <?php if($this->utility->isActiveTab('tags')) { ?> class="on" <?php } ?>>
    <a href="<?php $this->url->tagsView(); ?>"><i class="icon-tags"></i>Tags</a>
  </li>
  <li id="nav-search" <?php if($this->utility->isActiveTab('search')) { ?> class="on" <?php } ?>>
    <a role="button" class="nav-item-click"><i class="icon-search"></i>Search</a>
    <div id="searchbar">
      <form action="<?php $this->url->photosView(); ?>" method="get" id="form-tag-search">
        <input type="text" name="tags" placeholder="Enter a tag" class="select"><button type="submit" class="search-click">Search</button>
      </form>
    </div>
  </li>
  <?php if($this->user->isOwner()) { ?>
    <li id="nav-upload" <?php if($this->utility->isActiveTab('search')) { ?> class="on" <?php } ?>>
      <a href="<?php $this->url->photosUpload(); ?>"><i class="icon-upload-alt"></i>Upload</a>
    </li>
    <li id="nav-manage">
      <a href="<?php $this->url->userManage(); ?>"><i class="icon-cogs"></i>Manage</a>
    </li>
  <?php } ?>
  <?php if($this->user->isLoggedIn()) { ?>
    <li id="nav-signin">
      <?php echo $this->session->get('email'); ?><button class="settings-click"><img src="<?php $this->theme->asset('image', 'header-navigation-user.png'); ?>" class="settings-click"></button>
      <div id="settingsbar">
        <p><a href="<?php $this->url->userLogout(); ?>">Logout</a></p>
      </div>
    </li>
  <?php } else { ?>
    <li id="nav-signin">
      <button type="button" class="login-click browserid"><img src="https://browserid.org/i/sign_in_blue.png" alt="Signin to OpenPhoto" class="login-click browserid"></button>
    </li>
  <?php } ?>
</ul>
