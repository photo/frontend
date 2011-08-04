<div id="noPermission">
  <h2>Sorry, you don't have permission to view this page</h2>
  <?php if(User::isLoggedIn()) { ?>
    You are logged in as <?php echo getSession()->get('email'); ?>. <a href="/logout">Logout</a>.
  <?php } else { ?>
    <a href="#" class="login button">Login</a> to access this page.
  <?php } ?>
</div>
<br clear="all">
