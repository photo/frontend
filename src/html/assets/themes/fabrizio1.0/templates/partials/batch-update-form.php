<?php $buttonLabel = 'Save'; ?>
<?php if(isset($action) && !empty($action)) { ?>
  <?php // a few reasons we don't want to display the standard form ?>
  <?php if(
            ($action === 'albums' && isset($albums) && count($albums) === 0)
          ) { ?>
    <h4>You haven't created any albums, yet.</h4>
    <p>
      You can create albums from the <a href="/albums/list"><em>albums</em></a> tab. 
      Then come back, we'll hold your place in line.
    </p>
    <p>
      <a href="/albums/list" class="btn btn-theme-secondary btn-arrow">Go to albums</a>
    </p>
  <?php } else { ?>
    <form class="form-inline batch">
      <?php if($action == 'tags') { ?>
        <h4>What tags would you like to add or remove?</h4>
        <div class="control-group">
          <input type="text" name="tagsAdd" class="input-medium typeahead tags" autocomplete="off" placeholder="Tags to add or remove...">
        </div>
        <div class="control-group">
          <label class="radio inline">
            <input type="radio" name="mode" value="add" checked="checked" class="batchTagMode"> Add 
          </label>
          <label class="radio inline">
            <input type="radio" name="mode" value="remove" class="batchTagMode"> Remove 
          </label>
        </div>
      <?php } elseif($action == 'delete') { ?>
        <input type="hidden" name="delete" value="1">
        <?php $buttonLabel = "I'm absolutely sure"; ?>
        <h4>Are you sure you'd like to delete <?php if(isset($ids)) { ?>this photo<?php } else { ?> these photos<?php } ?>?</h4>
        <div class="row">
          <div class="alert alert-warning span4"><i class="icon-warning-sign"></i>This action cannot be undone</div>
        </div>
        <div class="control-group">
          <label class="checkbox">
            <input type="checkbox" name="confirm" value="1"> I'm sure 
          </label>
        </div>
        <div class="control-group">
          <label class="checkbox">
            <input type="checkbox" name="confirm2" value="remove"> I'm really sure 
          </label>
        </div>
      <?php } elseif($action == 'albums') { ?>
        <h4>Add to or remove from which album? <em><small>(Found <?php echo count($albums); ?> albums)</small></em></h4>
        <div class="control-group">
          <select name="albumsAdd" class="albums">
            <?php foreach($albums as $album) { ?>
              <option value="<?php $this->utility->safe($album['id']); ?>"><?php $this->utility->safe($album['name']); ?></option>
            <?php } ?>       
          </select>
        </div>
        <div class="control-group">
          <label class="radio inline">
            <input type="radio" name="mode" value="add" checked="checked" class="batchAlbumMode"> Add 
          </label>
          <label class="radio inline">
            <input type="radio" name="mode" value="remove" class="batchAlbumMode"> Remove 
          </label>
        </div>
      <?php } elseif($action == 'privacy') { ?>
        <h4>Change the privacy of your photos?</h4>
        <label class="radio inline">
          <input type="radio" name="permission" value="0" checked="checked"> Private 
        </label>
        <label class="radio inline">
          <input type="radio" name="permission" value="1"> Public 
        </label>
        &nbsp;&nbsp;
      <?php } elseif($action == 'tarball') {?>
      <?php
          $buttonLabel = "Download";
      ?>
      <?php if(isset($ids)) { ?>
        <input type="hidden" name="ids" value="<?php $this->utility->safe($ids); ?>">
      <?php } ?>
        <h4>Choose an output format</h4>
          <div class="control-group">
            <select class="chooseFormat" name="archive" >
              <?php $installed = shell_exec("which tar"); 
              if (empty($installed)?false:true) {?>
                <option value="tar">tar</option>
              <?php } ?>     
              <?php $installed = shell_exec("which zip"); 
              if (empty($installed)?false:true) {?>
                <option value="zip">zip</option>     
              <?php } ?>        
            </select>
          </div>
          <label class="checkbox">
            <input type="checkbox" name="gzip" value="true"> gzip?
          </label>
          <h4>... and a name for the file</h4>
          <label class="checkbox">
            <input id="archiveAlbumName" type="text" value="Images" name="name">
          </label>

        &nbsp;&nbsp;
      <?php } ?>      
      <button type="submit" class="btn btn-brand"><?php $this->utility->safe($buttonLabel); ?></button> or <a href="#" class="batchHide">cancel</a>
      <?php if(isset($ids)) { ?>
        <input type="hidden" name="ids" value="<?php $this->utility->safe($ids); ?>">
      <?php } ?>
    </form>
  <?php } ?>
<?php } ?>
<a href="#" class="batchHide close" title="Close this dialog"><i class="icon-remove batchHide"></i></a>
