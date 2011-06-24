<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="/assets/css/styles.css">
  <link rel="stylesheet" href="/assets/css/gh-buttons.css">
</head>
<body>
  <div id="site">
    <header>
      <h1>OpenPhoto</h1>
      <nav>
        <?php getTemplate()->display('header.php'); ?>
      </nav>
    </header>
    <div>
      <?php echo $body; ?>
    </div>
    <footer>
      <?php //getTemplate()->display('footer.php'); ?>
    </footer>
  </div>
  <script type="text/javascript" src="http://code.jquery.com/jquery-1.6.1.min.js"></script>
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
