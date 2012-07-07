  <form class="<?php if(!isset($_GET['modal'])) { ?>well <?php } ?>album-post-submit" action="/album/create">
    <?php if(!isset($_GET['modal'])) { ?><h3>Create a new album</h3><?php } ?>
    <label>Name</label>
    <input type="text" name="name">
    <input type="hidden" name="dynamic" value="<?php if(isset($_GET['dynamic'])) { ?>1<?php } else { ?>0<?php } ?>">
    
    <br>
    <button class="btn btn-primary"><i class="icon-save icon-large"></i> Create</button>
  </form>
