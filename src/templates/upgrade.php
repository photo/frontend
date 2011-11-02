<h1>We need to upgrade from <?php Utility::safe($lastVersion); ?> to <?php Utility::safe($currentVersion); ?></h1>

<p>
  Before you start the upgrade you'll want to make sure you have a backup of your database.
</p>

<?php if(!empty($readmes)) { ?>
  <?php foreach($readmes as $version => $readme) { ?>
    <h2>Notes for upgrading to <?php Utility::safe($version); ?></h2>
    <p>
      <?php echo $readme; // allow for html here?>
    <p>
  <?php } ?>
<?php } ?>

<p>
  <form action="/upgrade" method="post">
    <button type="submit">Begin upgrade</button>
  </form>
</p>
