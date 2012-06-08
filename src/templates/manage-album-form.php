<form class="<?php if(!isset($_GET['modal'])) { ?>well <?php } ?>album-post-submit" action="/album/create">
    <?php if(!isset($_GET['modal'])) { ?><h3>Create a new album</h3><?php } ?>
    <label>Name</label>
    <input type="text" name="name">
    <div class="control-group">
      <label class="control-label">Permission</label>
      <div class="controls">
        <label class="radio inline">
          <input type="radio" name="permission" id="public" value="1" checked="checked">
          Public
        </label>
        <label class="radio inline">
          <input type="radio" name="permission" id="private" value="0">
          Private
        </label>
      </div>
    </div>
      
    <?php if(count($groups) > 0) { ?>
      <div class="control-group">
        <label class="control-label">Groups</label>
        <select data-placeholder="Select groups for these photos" multiple  name="groups" class="typeahead">
          <?php foreach($groups as $group) { ?>
            <option value="<?php $this->utility->safe($group['id']); ?>"><?php $this->utility->safe($group['name']); ?></option>
          <?php } ?>
        </select>
      </div>
    <?php } ?>
    <input type="hidden" name="dynamic" value="<?php if(isset($_GET['dynamic'])) { ?>1<?php } else { ?>0<?php } ?>">
    <button class="btn btn-primary">Create</button>
  </form>
