<h4>Update your plugin: <em><?php $this->utility->safe($plugin); ?></em></h4>
<div class="row">
  <div class="span12">
    <form class="pluginUpdate" action="/plugin/<?php $this->utility->safe($plugin); ?>/update">
      <table class="table left-header">
        <?php foreach($conf as $k => $v) { ?>
          <tr>
            <td class="span2"><label><?php $this->utility->safe($k); ?></label></td>
            <td><input type="text" name="<?php $this->utility->safe($k); ?>" value="<?php $this->utility->safe($v); ?>"></td>
          </tr>
        <?php } ?>
        <tr>
          <td></td>
          <td>
            <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
            <button type="submit" class="btn btn-primary">Save</button>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<a href="#" class="batchHide close" title="Close this dialog"><i class="icon-remove batchHide"></i></a>



