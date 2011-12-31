<?php
$status = true;

$sql = <<<SQL
  ALTER TABLE  `{$this->mySqlTablePrefix}elementTag` ADD INDEX (  `element` )
SQL;
$status = $status && mysql_1_4_0($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
$status = $status && mysql_1_4_0($sql, array(':key' => 'version', ':version' => '1.4.0'));


function mysql_1_4_0($sql, $params = array())
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
