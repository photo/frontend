Please execute the following commands before upgrading.

<pre>mkdir <?php printf('%s/assets/cache', getConfig()->get('paths')->docroot); ?>

chown <?php printf('%s:%s %s/assets/cache', exec('whoami'), exec('whoami'), getConfig()->get('paths')->docroot); ?> </pre>
