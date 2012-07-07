<div class="manage features">
  <div class="row hero-unit blurb">
    <h2>Configure your OpenPhoto site</h2>
    
    <p>
      Don't want users to download your original photos? No problem. Want to allow the same photo to be uploaded twice? You're at the right spot.
    </p>    
  </div>
  
  <form class="well features-post-submit" action="/manage/features">
    <h3>General settings</h3>
    <div class="controls">
      <label class="checkbox inline">
        <input type="checkbox" name="allowDuplicate" value="1" <?php if($allowDuplicate) { ?>checked="checked"<?php } ?>>
        The same photo can be uploaded more than once
      </label>
    </div>
    <div class="controls">
      <label class="checkbox inline">
        <input type="checkbox" name="downloadOriginal" value="1" <?php if($downloadOriginal) { ?>checked="checked"<?php } ?>>
        Let visitors download my original hi-res photos
      </label>
    </div>
    <div class="controls">
      <label class="checkbox inline">
        <input type="checkbox" name="hideFromSearchEngines" value="1" <?php if($hideFromSearchEngines) { ?>checked="checked"<?php } ?>>
        Hide my site from search engines
      </label>
    </div>
    
    <div class="btn-toolbar"><button class="btn btn-primary">Save</button></div>
  </form>
</div>
