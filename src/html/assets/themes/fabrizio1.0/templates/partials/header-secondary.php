          <?php if($this->utility->isActiveTab('photo')) { ?>
            <li class="separator-left dropdown batch-meta"></li>
          <?php } elseif($this->utility->isActiveTab('upload')) {?>
            <li class="separator-left"><a href="#"><i class="tb-icon-light tb-icon-storage"></i> iPhone App</a></li>
            <li><a href=#"><i class="tb-icon-light tb-icon-storage"></i> Android App</a></li>
          <?php } ?>
