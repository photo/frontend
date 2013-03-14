<h4>Details for your app: <em><?php $this->utility->safe($name); ?></em></h4>
<div class="row">
  <div class="span12">
    <table class="table left-header">
      <tr>
        <td class="span2">Name</td>
        <td><?php $this->utility->safe($name); ?></td>
      </tr>
      <tr>
        <td>Consumer Key</td>
        <td><?php $this->utility->safe($id); ?></td>
      </tr>
      <tr>
        <td>Consumer Secret</td>
        <td><?php $this->utility->safe($clientSecret); ?></td>
      </tr>
      <tr>
        <td>OAuth Token</td>
        <td><?php $this->utility->safe($userToken); ?></td>
      </tr>
      <tr>
        <td>OAuth Secret</td>
        <td><?php $this->utility->safe($userSecret); ?></td>
      </tr>
      <tr>
        <td>Type</td>
        <td>
          <?php $this->utility->safe($type); ?>
          <?php if($type !== Credential::typeAccess) { ?>
            <small>(Only access tokens can be used)</small>
          <?php } ?>
        </td>
      </tr>
    </table>
  </div>
</div>
<a href="#" class="batchHide close" title="Close this dialog"><i class="icon-remove batchHide"></i></a>
