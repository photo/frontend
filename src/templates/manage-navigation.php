  <ul class="nav nav-pills">
    <li class="<?php if($page == 'home') { ?> active<?php } ?>">
      <a href="/manage">Photos</a>
    </li>
    <li class="<?php if($page == 'features') { ?> active<?php } ?>">
      <a href="/manage/features">Features</a>
    </li>
    <li class="<?php if($page == 'albums') { ?> active<?php } ?>">
      <a href="/manage/albums">Albums</a>
    </li>
    <li class="<?php if($page == 'groups') { ?> active<?php } ?>">
      <a href="/manage/groups">Groups</a>
    </li>
    <li class="<?php if($page == 'apps') { ?> active<?php } ?>">
      <a href="/manage/apps">Apps</a>
    </li>
    <li>
      <a href="/setup?edit">Restart Setup</a>
    </li>
  </ul>
