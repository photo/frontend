  <form class="plugin-update-submit" action="/plugin/<?php $this->utility->safe($plugin); ?>/update">
    <?php foreach($conf as $k => $v) { ?>
      <label><?php $this->utility->safe($k); ?></label>
      <input type="text" name="<?php $this->utility->safe($k); ?>" value="<?php $this->utility->safe($v); ?>">
    <?php } ?>
    
    <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
    <br>
    <button class="btn btn-primary"><i class="icon-save icon-large"></i> Save</button>
  </form>
