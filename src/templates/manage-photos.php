<div class="manage photos">
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

  <?php echo $pagination; ?>

  <!-- Add Select All button -->
  <div class="row" style="margin-bottom: 10px;">
    <div class="span12">
      <button id="select-all-btn" class="btn btn-primary">Select All</button>
    </div>
  </div>

  <div class="row hero-unit empty">
    <?php if($photos[0]['totalRows'] > 0) { ?>
      <ul class="thumbnails">
        <?php foreach($photos as $photo) { ?>
          <li class="span2">
            <img src="<?php $this->utility->safe($photo['path160x160']); ?>" style="width:<?php $this->utility->safe($photo['photo160x160'][1]); ?>px; <?php if($photo['photo160x160'][2] < 160) { ?> margin: <?php echo intval((160-$photo['photo160x160'][2])/2); ?>px auto;<?php } ?>" alt="<?php $this->utility->safe($photo['title']); ?>">
            <div>
              <i class="icon-ok icon-large photo-<?php $this->utility->safe($photo['id']); ?> pin-click pin reveal" data-id="<?php $this->utility->safe($photo['id']); ?>"></i>
              <i class="icon-edit icon-large photo-<?php $this->utility->safe($photo['id']); ?> photo-edit-click edit reveal" data-id="<?php $this->utility->safe($photo['id']); ?>"></i>
            </div>
          </li>
        <?php } ?>
      </ul>
    </div>
    <?php } else { ?>
      <?php $this->theme->display('partials/no-content.php'); ?>
    <?php } ?>

  <!-- JavaScript for Select All functionality -->
  <script>
  $(document).ready(function() {
    var selectAllBtn = $('#select-all-btn');
    var isAllSelected = false;
    
    selectAllBtn.click(function() {
      if (isAllSelected) {
        // Unselect all - clear the batch
        OP.Batch.clear();
        $('.pin').removeClass('selected');
        selectAllBtn.text('Select All');
        isAllSelected = false;
      } else {
        // Select all - add each photo to batch
        $('.pin').each(function() {
          var photoId = $(this).data('id');
          if (photoId && !OP.Batch.exists(photoId)) {
            OP.Batch.add(photoId);
            $(this).addClass('selected');
          }
        });
        selectAllBtn.text('Unselect All');
        isAllSelected = true;
      }
    });
    
    // Update button state based on current selections
    function updateButtonState() {
      var totalPhotos = $('.pin').length;
      var selectedPhotos = OP.Batch.length();
      
      if (selectedPhotos === totalPhotos && totalPhotos > 0) {
        selectAllBtn.text('Unselect All');
        isAllSelected = true;
      } else {
        selectAllBtn.text('Select All');
        isAllSelected = false;
      }
    }
    
    // Listen for batch changes
    if (typeof OP !== 'undefined' && OP.Util) {
      OP.Util.on('callback:batch-add', updateButtonState);
      OP.Util.on('callback:batch-remove', updateButtonState);
      OP.Util.on('callback:batch-clear', function() {
        selectAllBtn.text('Select All');
        isAllSelected = false;
      });
    }
  });
  </script>

</div>
