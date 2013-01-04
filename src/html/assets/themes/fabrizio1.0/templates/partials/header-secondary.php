          <?php if($this->utility->isActiveTab('photo')) { ?>
            <li class="separator-left dropdown batch-meta"></li>
          <?php } elseif($this->utility->isActiveTab('upload')) {?>
            <li class="separator-left"><a href="#"><i class="icon-camera-retro tb-icon-light icon-mobile-phone"></i> iPhone App</a></li>
            <li><a href=#"><i class="tb-icon-light icon-mobile-phone"></i> Android App</a></li>
          <?php } elseif($this->utility->isActiveTab('manage')) {?>
            <li class="separator-left"><a href="#settings"><i class="icon-cogs"></i> General Settings</a></li>
            <li><a href="#apps"><i class="icon-briefcase"></i> Applications</a></li>
            <li><a href="#plugins"><i class="icon-circle-blank"></i> Plugins</a></li>
          <?php } ?>
