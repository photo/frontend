  <form class="<?php if(!isset($_GET['modal'])) { ?>well <?php } ?>group-post-submit" action="/group/create">
    <?php if(!isset($_GET['modal'])) { ?><h3>Create a new group</h3><?php } ?>
    <label>Name</label>
    <input type="text" name="name">

    <label>Add an email address</label>
    <input type="text" class="group-email-input">&nbsp;&nbsp;&nbsp;<a href="#" class="group-email-add-click">Add</a>
    <ul class="group-emails-add-list unstyled">
    </ul>
    <input type="hidden" name="dynamic" value="<?php if(isset($_GET['dynamic'])) { ?>1<?php } else { ?>0<?php } ?>">
    <button class="btn btn-primary"><i class="icon-save icon-large"></i> Create</button>
  </form>

