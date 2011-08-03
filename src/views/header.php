    <header>
      <div>
        <div class="margin">
          <div id="logo"></div>
          <nav>
            <ul class="button-group">
              <!--<li class="home <?php echo Utility::isActiveTab('home') ? 'on' : ''; ?>"><a href="/"><img src="/assets/img/default/header-navigation-home.png" align="absmiddle">Home</a></li>-->
              <li class="photos <?php echo Utility::isActiveTab('photos') ? 'on' : ''; ?>"><a href="/photos"><img src="/assets/img/default/header-navigation-photos.png" align="absmiddle">Photos</a></li>
              <li class="tags <?php echo Utility::isActiveTab('tags') ? 'on' : ''; ?>"><a href="/tags"><img src="/assets/img/default/header-navigation-tags.png" align="absmiddle">Tags</a></li>
              <li class="search <?php echo Utility::isActiveTab('search') ? 'on' : ''; ?>"><a href="#" class="search-bar-toggle"><img src="/assets/img/default/header-navigation-search.png" align="absmiddle">Search</a></li>
              <li class="upload <?php echo Utility::isActiveTab('upload') ? 'on' : ''; ?>"><a href="/photos/upload"><img src="/assets/img/default/header-navigation-upload.png" align="absmiddle">Upload</a></li>
            </ul>
            <div class="user">
              <?php if(User::isLoggedIn()) { ?>
                <?php echo getSession()->get('email'); ?><img src="/assets/img/default/header-navigation-user.png" align="absmiddle">
              <?php } else { ?>
                <a href="#" class="login"><img src="https://browserid.org/i/sign_in_blue.png" align="absmiddle"></a>
              <?php } ?>
            </div>
            <div id="searchbar">
              <form method="get" id="form-tag-search">
                <input type="text" name="tags"> &nbsp; <button type="submit">Search</button>
              </form>
            </div>
          </nav>
        </div>
      </div>
    </header>
