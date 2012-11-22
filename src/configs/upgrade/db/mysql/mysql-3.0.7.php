<?php

$status = true;

/* add timestamp to the table credentials */
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}photoVersion` DROP INDEX `id` ,
  ADD UNIQUE `id` ( `owner` , `id` , `key` );
SQL;
$status = $status && mysql_3_0_7($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
$status = $status && mysql_3_0_7($sql, array(':key' => 'version', ':version' => '3.0.7'));

function mysql_3_0_7($sql, $params = array())
{
  try
  {
    getDatabase()->execute($sql, $params);
    getLogger()->info($sql);
  }
  catch(Exception $e)
  {
    getLogger()->crit($e->getMessage()); 
    return false;
  }
  return true;
}

return $status;
