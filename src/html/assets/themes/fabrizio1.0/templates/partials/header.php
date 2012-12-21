      <script type="tmpl/underscore" id="profile-photo-meta">
        <img class="profile-pic profile-photo" src="<%= photoUrl %>" />
      </script>
      <script type="tmpl/underscore" id="profile-name-meta">
        <%= name %>
      </script>
      <!-- Logo and Primary Navigation -->
      <div class="navbar-inner navbar-inner-primary">
        <div class="container">
          <h1 class="logo"><a href="/">TroveBox</a></h1>
          <div class="nav-collapse collapse">
            <ul class="nav separator-left">
              <li class="active active-page-item"><a href="<?php $this->url->photosView(); ?>"><i class="tb-icon-gallery tb-icon-highlight"></i> Gallery</a></li>
              <li><a href="<?php $this->url->albumsView(); ?>"><i class="tb-icon-albums tb-icon-dark"></i> Albums</a></li>
              <?php if($this->user->isAdmin()) { ?>
                <li><a href="<?php $this->url->photosUpload(); ?>"><i class="tb-icon-upload tb-icon-dark"></i> Upload</a></li>
              <?php } ?>
            </ul>
          </div><!--/.nav-collapse -->
          <div class="search-wrap separator-left">
            <input type="search" name="search" placeholder="Search Tags..."/>
          </div>
          <?php if($this->user->isLoggedIn()) { ?>
            <div class="user">
              <a href="#" class="profile-link" data-toggle="dropdown"><span class="profile-photo-meta"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">Child Item 1</a></li>
                <li><a href="#">Child Item 2</a></li>
              </ul>
            </div>
          <?php } else { ?>
            <div class="user">
              <?php if($this->config->site->displaySignupLink == 1) { ?>
              <a href="<?php $this->utility->safe($this->config->site->displaySignupUrl); ?>" class="btn btn-brand btn-arrow">Sign Up</a>
              <?php } ?>
              <a href="/signin" class="btn btn-theme-secondary">Sign In</a>
            </div>
          <?php } ?>
        </div>
      </div>

