<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="/assets/css/styles.css">
  <link rel="stylesheet" href="/assets/css/gh-buttons.css">
</head>
<body>
  <div id="site">
    <?php getTemplate()->display('header.php'); ?>
    <div id="content" class="margin">
      <?php echo $body; ?>
    </div>
    <footer>
      <?php getTemplate()->display('footer.php'); ?>
    </footer>
  </div>
  <script type="text/javascript" src="http://code.jquery.com/jquery-1.6.1.min.js"></script>
  <script src="https://browserid.org/include.js" type="text/javascript"></script>
  <?php if(isset($jsFiles)) { ?>
    <?php foreach($jsFiles as $file) { ?>
      <script type="text/javascript" src="<?php echo $file; ?>"></script>
    <?php } ?>
  <?php } ?>
  <script type="text/javascript" src="/assets/js/openphoto.js"></script>
  <script>
      $(document).ready(function() {
        op.init.attach();
        <?php if(isset($js)) { ?>
          <?php echo $js; ?>
        <?php } ?>
      });
  </script>
</body>
</html>
