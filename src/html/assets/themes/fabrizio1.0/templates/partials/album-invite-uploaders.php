<div class="row">
  <div class="span12"><h4>Invite others to upload to <em><?php $this->utility->safe($album['name']); ?></em></h4></div>
</div>
<div class="row">
  <div class="span6">
    <p>
      Your upload token is <a href="/photos/upload/<?php $this->utility->safe($token['id']); ?>"><?php $this->utility->safe($token['id']); ?></a>. You can email, text or instant message this URL to anyone you'd like to upload photos into this album.
    </p>
    <p>
    <button class="btn btn-brand copyToClipboard addSpinner" data-clipboard-text="<?php $this->utility->safe(sprintf('%s://%s/photos/upload/%s', $this->utility->getProtocol(false), $this->utility->getHost(), $this->utility->safe($token['id'], false))); ?>">Copy URL to clipboard</button>
    </p>
    <p>
      <i class="icon-info-sign"></i> 
      <?php if($token['dateExpires'] == 0) { ?>
        This URL never expires.
      <?php } else { ?>
        This URL expires in <?php echo intval(($token['dateExpires']-time())/86400); ?> days. <small>(<?php echo $this->utility->dateLong($token['dateExpires']); ?>)</small>
      <?php } ?>
    </p>
  </div>
</div>
<a href="#" class="batchHide close" title="Close this dialog"><i class="icon-remove batchHide"></i></a>
