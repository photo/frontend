  <form class="<?php if(!isset($_GET['modal'])) { ?>well <?php } ?>album-post-submit" action="/album/create">
    <?php if(!isset($_GET['modal'])) { ?><h3>Create a new album</h3><?php } ?>
    <label>Name</label>
    <input type="text" name="name">
    <input type="hidden" name="dynamic" value="<?php if(isset($_GET['dynamic'])) { ?>1<?php } else { ?>0<?php } ?>">
    
    <div class="control-group">
      <label class="control-label">Include on Albums page</label>
      <div class="controls">
        <label class="radio inline">
          <input type="radio" name="visible" value="1" checked="checked">
          Public
        </label>
        <label class="radio inline">
          <input type="radio" name="visible" value="0">
          Private
        </label>
      </div>
    </div>
    
    <br>
    <button class="btn btn-primary"><i class="icon-save icon-large"></i> Create</button>
  </form>
