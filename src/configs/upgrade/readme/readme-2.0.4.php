If you're upgrading MySql from 2.0.0 then you'll have to run the following query. Replace <em>{prefix}</em> with any table prefix you're using.
<br><br>
<strong>Do this before continuing!</strong>
<br><br>
<code>UPDATE `{prefix}admin` SET `value`='2.0.0' WHERE `key`='version';</code>
