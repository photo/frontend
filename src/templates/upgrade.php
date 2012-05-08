<h1>We need to upgrade from <?php $this->utility->safe($lastVersion); ?> to <?php $this->utility->safe($currentVersion); ?></h1>

<p>
  Before you start the upgrade you'll want to make sure you have a backup of your database.
</p>

<?php if(!empty($readmes)) { ?>
  <?php foreach($readmes as $version => $readme) { ?>
    <h2>Notes for upgrading to <?php $this->utility->safe($version); ?></h2>
    <p>
      <?php echo $readme; // allow for html here?>
    <p>
  <?php } ?>
<?php } ?>

<p>
  <form action="/upgrade" method="post">
    <button type="submit" class="btn btn-primary">Begin upgrade</button>
  </form>
</p>
