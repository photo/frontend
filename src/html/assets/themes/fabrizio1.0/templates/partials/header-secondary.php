          <?php if($this->utility->isActiveTab('albums')) { ?>
            <?php if($this->user->isAdmin()) { ?>
              <li class="separator-left batch-meta"></li>
            <?php } ?>
          <?php } else if($this->utility->isActiveTab('photo')) { ?>
            <?php if($this->user->isAdmin() || $this->config->site->allowOriginalDownload == 1) { ?>
              <li class="separator-left"><a href="#" class="triggerDownload"><i class="icon-download triggerDownload"></i> Download</a></li>
            <?php } ?>
            <?php if($this->user->isAdmin()) { ?>
              <li><a href="#" class="triggerShare"><i class="icon-share-alt triggerShare"></i> Share</a></li>
            <?php } ?>
          <?php } else if($this->utility->isActiveTab('photos')) { ?>
            <?php if($this->user->isAdmin()) { ?>
              <li class="separator-left"><a href="#" class="selectAll"><i class="icon-pushpin"></i> Select all</a></li>
              <li class="dropdown batch-meta"></li>
            <?php } ?>
          <?php } elseif($this->utility->isActiveTab('upload')) {?>
            <li class="separator-left"><a href="http://bit.ly/trovebox-for-iphone"><i class="icon-camera-retro icon-mobile-phone"></i> iPhone App</a></li>
            <li><a href="http://bit.ly/trovebox-for-android"><i class="icon-mobile-phone"></i> Android App</a></li>
          <?php } elseif($this->utility->isActiveTab('manage')) {?>
            <li class="separator-left"><a href="#settings"><i class="icon-cogs"></i> General Settings</a></li>
            <li><a href="#apps"><i class="icon-briefcase"></i> Applications</a></li>
            <li><a href="#plugins"><i class="icon-circle-blank"></i> Plugins</a></li>
          <?php } ?>
