<ul>
  <li id="nav-photos" <?php if(Utility::isActiveTab('photos')) { ?> class="on" <?php } ?>>
    <a href="/photos" class="nav-item">Photos</a>
  </li>
  <li id="nav-tags" <?php if(Utility::isActiveTab('tags')) { ?> class="on" <?php } ?>>
    <a href="/tags" class="nav-item">Tags</a>
  </li>
  <li id="nav-search" <?php if(Utility::isActiveTab('search')) { ?> class="on" <?php } ?>>
    <a id="search-bar-toggle" role="button" class="nav-item">Search</a>
    <div id="searchbar" class="offscreen">
      <form method="get" id="form-tag-search">
        <input type="text" name="tags" placeholder="Enter a tag" class="select"><button type="submit">Search</button>
      </form>
    </div>
  </li>
  <?php if(User::isOwner()) { ?>
  <li id="nav-upload" <?php if(Utility::isActiveTab('search')) { ?> class="on" <?php } ?>>
    <a href="/upload" class="nav-item">Upload</a>
  </li>
  <?php } ?>
  <?php if(User::isLoggedIn()) { ?>
  <li id="nav-signin">
    <?php echo getSession()->get('email'); ?><img src="<?php getTheme()->asset('image', 'header-navigation-user.png'); ?>" align="absmiddle">
  </li>
  <?php } else { ?>
  <li id="nav-signin">
    <button class="login"><img src="https://browserid.org/i/sign_in_blue.png" alt="Signin to OpenPhoto"></button>
    <!--<a href="#" class="login"><img src="https://browserid.org/i/sign_in_blue.png" align="absmiddle"></a>-->
  </li>
  <?php } ?>
</ul>
