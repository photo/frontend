          <?php if($this->utility->isActiveTab('photo')) { ?>
            <li><a href="#"><i class="tb-icon-light tb-icon-heart"></i> <span class="badge badge-important">3</span></a></li>
            <li><a href="#"><i class="tb-icon-light tb-icon-comment"></i> <span class="badge badge-important">6</span></a></li>
            <li class="separator-left"><a href="#"><i class="tb-icon-light tb-icon-heart"></i> My Favorites</a></li>
            <li class="dropdown"><a data-toggle="dropdown" href="#"><i class="tb-icon-light tb-icon-gear"></i> Manage</a>
              <ul class="dropdown-menu">
                <li><a href="#">Child Item 1</a></li>
                <li><a href="#">Child Item 2</a></li>
              </ul>
            </li>
            <li class="dropdown"><a data-toggle="dropdown" href="#"><i class="tb-icon-light tb-icon-plus"></i> Create New</a>
              <ul class="dropdown-menu">
                <li><a href="#">Child Item 1</a></li>
                <li><a href="#">Child Item 2</a></li>
              </ul>
            </li>
            <li class="dropdown"><a data-toggle="dropdown" href="#"><i class="tb-icon-light tb-icon-chart"></i> Statistics</a>
              <ul class="dropdown-menu">
                <li><a href="#">Child Item 1</a></li>
                <li><a href="#">Child Item 2</a></li>
              </ul>
            </li>
          <?php } ?>
