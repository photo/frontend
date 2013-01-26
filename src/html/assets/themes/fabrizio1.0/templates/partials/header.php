    <!-- Logo and Primary Navigation -->
    <div class="navbar-inner navbar-inner-primary">
      <div class="container">
        <h1 class="logo"><a href="/">Trovebox</a></h1>
        
        <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".primary-navigation">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        
        <?php if($this->user->isLoggedIn()) { ?>
          <div class="user">
            <a href="#" class="profile-link profile-photo-header-meta" data-toggle="dropdown"></a>
            <ul class="dropdown-menu" role="menu">
              <?php if($this->session->get('site') != '' && $this->utility->gethost() != $this->session->get('site')) { ?>
                <li><a href="<?php printf('%s://%s', $this->utility->getProtocol(false), $this->utility->safe($this->session->get('site'), false)); ?>">Back to my site</a></li>
              <?php } ?>
              <li><a href="/user/logout">Logout</a></li>
            </ul>
          </div>
        <?php } else { ?>
          <div class="user">
            <?php if($this->config->site->displaySignupLink == 1) { ?>
              <a href="<?php $this->utility->safe($this->config->site->displaySignupUrl); ?>" class="btn btn-brand btn-arrow">Sign Up</a>
            <?php } ?>
            <a href="/user/login?r=<?php $this->utility->safe($_SERVER['REQUEST_URI']); ?>" class="btn btn-theme-secondary">Sign In</a>
          </div>
        <?php } ?>
        
        <div class="nav-collapse collapse primary-navigation">
          <ul class="nav separator-left">
            <li class="<?php if($this->utility->isActiveTab('photos')) { ?> active active-page-item<?php } ?>"><a href="<?php $this->url->photosView(); ?>"><i class="icon-picture"></i> Gallery</a></li>
            <li class="<?php if($this->utility->isActiveTab('albums')) { ?> active active-page-item<?php } ?>"><a href="<?php $this->url->albumsView(); ?>"><i class="icon-th-large"></i> Albums</a></li>
            <li class="<?php if($this->utility->isActiveTab('tags')) { ?> active active-page-item<?php } ?>"><a href="<?php $this->url->tagsView(); ?>"><i class="icon-tags"></i> Tags</a></li>
            <?php if($this->user->isAdmin()) { ?>
              <li class="hidden-phone hidden-tablet <?php if($this->utility->isActiveTab('upload')) { ?> active active-page-item<?php } ?>"><a href="<?php $this->url->photosUpload(); ?>"><i class="icon-upload"></i> Upload</a></li>
            <?php } ?>
          </ul>
          <!--<div class="search-wrap separator-left">
            <input type="search" name="search" placeholder="Search Tags..."/>
          </div>-->
        </div><!--/.nav-collapse -->
      </div>
    </div>
    <div class="navbar-inner navbar-inner-secondary">
      <div class="container">
        <ul class="nav">
          <li><a href="/"><i class="icon-home"></i></a></li>
          <li class="separator-left"><span class="profile-name-meta owner"></span></li>
          <?php $this->theme->display('partials/header-secondary.php', array()); ?>
        </ul>
        <ul class="nav pull-right">
          <?php if($this->user->isAdmin()) { ?>
            <li><a href="/manage/settings"><i class="icon-cog"></i> Site Settings</a></li>
          <?php } ?>
          <li><div class="help-container"><a href="https://trovebox.com/faq"><i class="icon-question-sign"></i></div></a>
        </ul>
      </div>
      <div class="container">
        <div class="row secondary-flyout"></div>
      </div>
    </div>
    <div class="notification-meta"></div>
    
