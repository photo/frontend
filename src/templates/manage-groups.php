<div class="manage groups">

  <div class="row hero-unit blurb">
    <h2>What are groups?</h2>
    <p>
      Groups allow you to explicitly share private photos with others.
      <br>
      For example, say you have a photo marked as private but want <em>joe@example.com</em> see it.
      <ol>
        <li>Make sure <em>joe@example.com</em> belongs to a group</li>
        <li>Mark the photo as private</li>
        <li>Add the photo to a group <em>joe@example.com</em> is a member of</li>
      </ol>
      When Joe signs in he will be able to see the photo but others won't.
    </p>
  </div>

  <?php echo $groupAddForm; ?>

  <?php foreach($groups as $group) { ?>
    <a name="group-<?php $this->utility->safe($group['id']); ?>"></a>
    <form class="well group-post-submit" action="/group/<?php $this->utility->safe($group['id']); ?>/update">
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
      <button class="btn"><i class="icon-save icon-large"></i> Save</button>&nbsp;&nbsp;&nbsp;<a class="group-delete-click" href="/group/<?php $this->utility->safe($group['id']); ?>/delete">Or delete</a>
    </form>
  <?php } ?>
</div>
