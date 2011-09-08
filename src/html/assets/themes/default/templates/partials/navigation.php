<ul>
  <li id="nav-photos" <?php if(Utility::isActiveTab('photos')) { ?> class="on" <?php } ?>>
    <a href="/photos">Photos</a>
  </li>
  <li id="nav-tags" <?php if(Utility::isActiveTab('tags')) { ?> class="on" <?php } ?>>
    <a href="/tags">Tags</a>
  </li>
  <li id="nav-search" <?php if(Utility::isActiveTab('search')) { ?> class="on" <?php } ?>>
    <a role="button" class="nav-item-click">Search</a>
    <div id="searchbar">
      <form method="get" id="form-tag-search">
        <input type="text" name="tags" placeholder="Enter a tag" class="select"><button type="submit" class="search-click">Search</button>
      </form>
    </div>
  </li>
  <?php if(User::isOwner()) { ?>
  <li id="nav-upload" <?php if(Utility::isActiveTab('search')) { ?> class="on" <?php } ?>>
    <a href="/photos/upload">Upload</a>
  </li>
  <?php } ?>
  <?php if(User::isLoggedIn()) { ?>
  <li id="nav-signin">
    <?php echo getSession()->get('email'); ?><button class="settings-click"><img src="<?php getTheme()->asset('image', 'header-navigation-user.png'); ?>" class="settings-click"></button>
    <div id="settingsbar">
      <p><a href="/user/logout">Logout</a></p>
    </div>
  </li>
  <?php } else { ?>
  <li id="nav-signin">
    <button type="button" class="login-click"><img src="https://browserid.org/i/sign_in_blue.png" alt="Signin to OpenPhoto" class="login-click"></button>
  </li>
  <?php } ?>
</ul>
