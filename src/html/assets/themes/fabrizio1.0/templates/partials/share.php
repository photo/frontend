<h4>Share this photo via email or social networks</h4>
<div class="row">
  <div class="span3 preview">
    <img src="<?php $this->utility->safe($photo['path200x200']); ?>" class="img-polaroid">
    <small><?php $this->utility->safe($photo['url']); ?></small>
  </div>
  <?php if($this->user->isAdmin()) { ?>
    <div class="span3">
      <form class="shareEmail">
        <strong>Who would you like to email this photo to?</strong>
        <input type="text" class="span3" placeholder="a@gmail.com, b@yahoo.com" name="recipients">
        <textarea class="span3" placeholder="Type a message..." name="message"></textarea>
        <input type="hidden" name="attachment" value="1">
        <!--<label class="checkbox">
          <input type="checkbox" checked="checked" name="attachment" value="1"> Send as attachment
        </label>-->
        <button class="btn btn-brand">Send Email</button>
        <input type="hidden" name="ids" value="<?php $this->utility->safe(implode(',', $ids)); ?>">
        <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
        <input type="hidden" name="httpCodes" value="*">
      </form>
    </div>
  <?php } ?>
  <div class="span3 offset1 social">
    <strong>Or share on the following sites</strong>
    <ul class="unstyled">
      <li><a href="https://twitter.com/share?text=<?php echo urlencode('View my photo shared via @Trovebox.'); ?>&url=<?php echo urlencode($photo['url']); ?>" class="sharePopup btn btn-theme-secondary" target="blank"><i class="icon-twitter"></i> Post to Twitter</a></li>
      <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($photo['url']); ?>" class="sharePopup btn btn-theme-secondary"><i class="icon-facebook"></i> Post to Facebook</a></li>
    </ul>
  </div>
</div>
<a href="#" class="batchHide close" title="Close this dialog"><i class="icon-remove batchHide"></i></a>
