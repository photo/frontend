            <div class="span1 profile">
              <img src="<?php $this->utility->safe($this->user->getAvatarFromEmail(50, $activity[0]['owner'])); ?>" alt="church" class="thumbnail" />
            </div>
            <div class="span8 activity">
              <h3>
                <span class="capitalize"><?php $this->utility->safe($this->utility->getEmailHandle($activity[0]['owner'], false)); ?></span> uploaded <?php printf('%d %s', count($activity), $this->utility->plural(count($activity), 'photo', false)); ?>, 
                <em><?php $this->utility->timeAsText($activity[0]['data']['dateUploaded']); ?></em>
              </h3>
              <p>
                <?php foreach($activity as $activityDetails) { ?>
                  <a href="<?php $this->url->photoView($activityDetails['data']['id']); ?>"><img src="<?php $this->utility->safe($activityDetails['data']['path100x100xCR']); ?>" class="activityfeed-thumbnail"></a>
                <?php } ?>
              </p>
            </div>
