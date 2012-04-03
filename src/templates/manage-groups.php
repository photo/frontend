<div class="manage groups">

  <?php echo $navigation; ?>

  <form class="well" action="/group/create">
    <h3>Create a new group</h3>
    <label>Name</label>
    <input type="text" name="name">

    <label>Add an email address</label>
    <input type="text" class="group-email-input">&nbsp;&nbsp;&nbsp;<a href="#" class="group-email-add-click">Add</a>
    <ul class="group-emails-add-list unstyled">
    </ul>
    <a class="btn btn-primary group-post-click">Create</a>
  </form>

  <?php foreach($groups as $group) { ?>
    <form class="well" action="/group/<?php $this->utility->safe($group['id']); ?>/update">
      <h3>Edit <?php $this->utility->safe($group['name']); ?></h3>
      <label>Name</label>
      <input type="text" name="name" value="<?php $this->utility->safe($group['name']); ?>">

      <label>Add an email address</label>
      <input type="text" class="group-email-input">&nbsp;&nbsp;&nbsp;<a href="#" class="group-email-add-click">Add</a>
      <ul class="group-emails-add-list unstyled">
        <?php foreach($group['members'] as $member) { ?>
          <li><span class="group-email-queue"><?php $this->utility->safe($member); ?></span> <a href="#" class="group-email-remove-click"><i class="group-email-remove-click icon-minus-sign"></i></a></li>
        <?php } ?>
      </ul>
      <a class="btn group-post-click">Save</a>&nbsp;&nbsp;&nbsp;<a class="group-delete-click" href="/group/<?php $this->utility->safe($group['id']); ?>/delete">Or delete</a>
    </form>
  <?php } ?>
</div>
