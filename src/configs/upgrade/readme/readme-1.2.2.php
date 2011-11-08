<?php
  $userdataDirectory = Utility::safe(sprintf('%s/userdata', dirname(getConfig()->get('paths')->libraries)), false);
?>
<style type="text/css">
pre {
	font-size: 12px;
	padding: 0;
	margin: 10px;
	background: #f0f0f0;
	border-left: 1px solid #ccc;
	overflow: auto; /*--If the Code exceeds the width, a scrolling is available--*/
	overflow-Y: hidden;  /*--Hides vertical scroll created by IE--*/
}
pre code {
	margin: 0 0 0 20px;  /*--Left Margin--*/
  padding:0;
	display: block;
}
</style>
This upgrade requires that you create a new directory. Execute the following command or create it using an FTP client.
<br><br>
<div>
  Create a directory named <em>userdata</em>.
  <pre><code>mkdir <?php echo $userdataDirectory; ?></code></pre>
</div>
<div>
  Change ownership to webserver user <em><?php echo exec('whoami'); ?></em>. This may require sudo.
  <pre><code>chown <?php echo sprintf('%s %s', exec('whoami'), $userdataDirectory); ?></code></pre>
</div>
