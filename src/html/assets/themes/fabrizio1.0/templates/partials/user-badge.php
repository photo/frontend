<div class="userbadge">
  <h4 class="username"><?php $this->utility->safe($this->user->getNameFromEmail($this->config->user->email)); ?></h4>
  <div class="tray-wrap">
  <img src="<?php $this->utility->safe($this->user->getAvatarFromEmail($this->config->user->email)); ?>" title="<?php $this->utility->safe($this->user->getNameFromEmail($this->config->user->email)); ?>" class="avatar" />
    <div class="tray">
      <div class="details">
        <ul>
          <li>
            <a href="#">
              <i class="tb-icon-gallery"></i>
              <span class="number">1,722</span>
              <span class="title">photos</span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="tb-icon-albums"></i>
              <span class="number">8</span>
              <span class="title">albums</span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="tb-icon-tag"></i>
              <span class="number">230</span>
              <span class="title">tags</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

