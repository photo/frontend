<?php
/*
$this->theme->display('partials/user-badge.php');

?>
<?php if(!empty($activities)) { ?>
  <ol>
    <?php foreach($activities as $activity) { ?>
        <?php $this->theme->display(sprintf('partials/feed-%s.php', $activity[0]['type']), array('activity' => $activity)); ?>
      <?php $i++; ?>
    <?php } ?>
  </ol>
<?php
}
*/
?>
<div class="row">
  <div class="span8">
    <h3 class="sidebar-heading activity-list-heading">
      <i class="icon-angle-down"></i>
      <i class="icon-inbox"></i>
      Latest Activity
    </h3>
    <ul class="activity-list">
      <li class="new">
        <div class="activity-meta">
          <span class="activity-time">2 Hours</span>
          <i class="activity-type icon-heart"></i>
        </div>
        <img class="to" src="http://openphotofabrizio.s3.amazonaws.com/custom/201212/7d0cae-IMG_4413_100x100xCR.jpg" />
        <img class="from" src="http://openphotofabrizio.s3.amazonaws.com/custom/201212/7d0cae-IMG_4413_100x100xCR.jpg" />
        <div class="activity-wrap">
          <span class="activity-content">
            <a href="#">John Fabrizio</a> added your photo to his favorites.
          </span>
        </div>
      </li>
      <li>
        <div class="activity-meta">
          <span class="activity-time"></span>
          <i class="activity-type icon-comment"></i>
        </div>
        <img class="to" src="http://openphotofabrizio.s3.amazonaws.com/custom/201212/7d0cae-IMG_4413_100x100xCR.jpg" />
        <img class="from" src="http://openphotofabrizio.s3.amazonaws.com/custom/201212/7d0cae-IMG_4413_100x100xCR.jpg" />
        <div class="activity-wrap">
          <span class="activity-content">
            <a href="#">Mark Fabrizio</a> commented on your photo "Baby picture". "Enough already this is a really long comment that should wrap around..."
          </span>
        </div>
      </li>
    </ul>
  </div>
  <div class="span4">
    <ul class="sidebar">
      <li>
        <h3>Your Account</h3>
        <span class="notice-text">Your free trial account will expire in 6 days.</span>
        <p>You can still use TroveBox after the trial with some limitations.</p>
        <div class="actions">
          <a class="btn btn-theme-tertiary" style="margin-right: 10px" href="#">Upgrade Now</a> <a class="alt" href="#">or learn more</a>
        </div>
      </li>
      <li>
        <h3>Invite Your Friends</h3>
        <p>Know anybody that would like to try TroveBox? Both you and your friend get a month
        of Premium membership for each signup! (up to 3 months)</p>
        <p><a href="#">Tell your friends now <i class="icon-angle-right"></i></a></p>
      </li>
      <li>
        <h3>Download the App!</h3>
        <p>Take TroveBox wherever you go&mdash;download the app for iOS or Android</p>
        <p>
          <a href="#"><img src="<?php echo $this->theme->asset('image', 'download-ios.jpg') ?>" /></a>
        </p>
        <p>
          <a href="#"><img src="<?php echo $this->theme->asset('image', 'download-android.jpg') ?>" /></a>
        </p>
      </li>
      <li>
        <h3>TroveBox News</h3>
        <p><a href="#">Just updated! Our iOS app now supports full resolution downloads.</a></p>
        <p><a href="#">Refer friends and family and get 3 months free!</a></p>
        <p><a href="#">TroveBox reviewed on Mashable</a></p>
        
      </li>
    </ul>
  </div>
</div>



<div class="photo-grid">
  <div class="photo-grid-hr"></div>
</div>
<script> var initData = <?php echo json_encode($photos); ?>;</script>