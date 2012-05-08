<div class="manage home">

  <?php echo $navigation; ?>

  <div class="row hero-unit blurb">
    <h2>Tips on updating your photos</h2>
    <p>
      You can easily edit title, tags, permission and licensing of your photos. 
      When you hover over a photo you'll see two icons.
      <ol>
        <li>The <i class="icon-edit icon-large"></i> <em>pencil</em> icon brings up a dialog to edit that photo.</li>
        <li>The <i class="icon-ok icon-large"></i> <em>checkmark</em> queues that photo in case you wanted to update multiple photos at once.</li>
      </ol>
    </p>
  </div>

  <?php if($photos[0]['totalRows'] > 0) { ?>
    <?php echo $pagination; ?>
    <ul class="thumbnails">
      <?php foreach($photos as $photo) { ?>
        <li class="span2">
          <img src="<?php $this->utility->safe($photo['path160x160xCR']); ?>" alt="<?php $this->utility->safe($photo['title']); ?>" class="thumbnail">
          <i class="icon-ok icon-large photo-<?php $this->utility->safe($photo['id']); ?> pin-click pin reveal" data-id="<?php $this->utility->safe($photo['id']); ?>"></i>
          <i class="icon-edit icon-large photo-<?php $this->utility->safe($photo['id']); ?> photo-edit-click edit reveal" data-id="<?php $this->utility->safe($photo['id']); ?>"></i>
        </li>
      <?php } ?>
    </ul>
  <?php } else { ?>
    <?php $this->theme->display('partials/no-content.php'); ?>
  <?php } ?>
</div>
