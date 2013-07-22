<h4>Share this <?php $this->utility->safe($type); ?> via email or social networks</h4>
<div class="row">
  <div class="span3 preview">
    <?php if($type === 'album') { ?>
      <div class="cover">
        <span class="stack stack1"></span>
        <span class="stack stack2"></span>
        <img src="<?php $this->utility->safe($photo); ?>" class="img-polaroid">
      </div>
    <?php } else { ?>
      <img src="<?php $this->utility->safe($photo); ?>" class="img-polaroid">
    <?php } ?>
    <small><a href="<?php $this->utility->safe($url); ?>" class="black" title="Copy this link to share it manually"><?php $this->utility->safe($url); ?></a></small>
  </div>
  <?php if($this->user->isAdmin()) { ?>
    <div class="span3">
      <form class="shareEmail">
        <strong>Who would you like to email this <?php $this->utility->safe($type); ?> to?</strong>
        <input type="text" class="span3" placeholder="a@gmail.com, b@yahoo.com" name="recipients">
        <textarea class="span3" placeholder="Type a message..." name="message"></textarea>
        <input type="hidden" name="attachment" value="1">
        <!--<label class="checkbox">
          <input type="checkbox" checked="checked" name="attachment" value="1"> Send as attachment
        </label>-->
        <button class="btn btn-brand">Send Email</button>
        <input type="hidden" name="type" value="<?php $this->utility->safe($type); ?>">
        <input type="hidden" name="data" value="<?php $this->utility->safe(implode(',', $data)); ?>">
        <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
        <input type="hidden" name="url" value="<?php $this->utility->safe($url); ?>">
        <input type="hidden" name="httpCodes" value="*">
      </form>
    </div>
  <?php } ?>
  <div class="span3 offset1 social">
    <strong>Share using Facebook, Twitter or a blog</strong>
    <p>
      <?php if($type === 'photo') { ?>
        <small>Your photo is private. Anyone with this link can view it.</small>
      <?php } else { ?>
        <small>Anyone with this link can view all photos in this album.</small>
      <?php } ?>
    </p>
    <ul class="unstyled">
      <li><a href="https://twitter.com/share?text=<?php echo urlencode('View my photo shared via @Trovebox.'); ?>&url=<?php echo urlencode($url); ?>" class="sharePopup btn btn-theme-secondary" target="blank"><i class="icon-twitter"></i> Post to Twitter</a></li>
      <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url); ?>" class="sharePopup btn btn-theme-secondary"><i class="icon-facebook"></i> Post to Facebook</a></li>
        <?php if($type === 'photo') { ?>
          <li>
            <a href="#" class="toggle btn btn-theme-secondary" data-target=".social pre.share-embed-code"><i class="icon-list-alt"></i> Embed in blog</a>
            <pre class="hide share-embed-code"><?php echo htmlspecialchars(<<<MKP
<a href="{$url}">
  <img src="{$photoLarge}">
</a>
MKP
              ); ?></pre>
        </li>
      <?php } ?>
    </ul>
    <small>
      <i class="icon-info-sign"></i>
      This link contains a sharing token.
      It's an easy way to share photos without requiring them to be public.
    </small>
  </div>
</div>
<a href="#" class="batchHide close" title="Close this dialog"><i class="icon-remove batchHide"></i></a>
