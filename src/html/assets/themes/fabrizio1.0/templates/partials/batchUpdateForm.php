<?php if(isset($action) && !empty($action)) { ?>
  <br>
  <?php // a few reasons we don't want to display the standard form ?>
  <?php if(
            ($action === 'albumsAdd' && isset($albums) && count($albums) === 0)
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
      <?php if($action == 'tagsAdd') { ?>
        <h4>What tags would you like to add?</h4>
        <input type="text" name="tagsAdd" class="input-medium" placeholder="Type tags to add...">
      <?php } elseif($action == 'albumsAdd') { ?>
        <h4>Which album would you like to add? <em><small>(Found <?php echo count($albums); ?> albums)</small></em></h4>
        <select name="albumsAdd">
          <?php foreach($albums as $album) { ?>
            <option value="<?php $this->utility->safe($album['id']); ?>"><?php $this->utility->safe($album['name']); ?></option>
          <?php } ?>       
        </select>
      <?php } elseif($action == 'privacy') { ?>
        <h4>Change the privacy of your photos?</h4>
        <label class="radio inline">
          <input type="radio" name="permission" value="0" checked="checked"> Private 
        </label>
        <label class="radio inline">
          <input type="radio" name="permission" value="1"> Public 
        </label>
        &nbsp;&nbsp;
      <?php } ?>
      <button type="submit" class="btn btn-theme-secondary">Save</button> or <a href="#" class="batchHide">cancel</a>
    </form>
  <?php } ?>
<?php } ?>

