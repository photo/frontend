<?php

$status = true;

$sql = <<<SQL
  ALTER TABLE  `{$this->mySqlTablePrefix}user` ADD  `password` VARCHAR( 64 ) NOT NULL AFTER  `id`;
SQL;
$status = $status && mysql_2_0_2($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key;
SQL;
$status = $status && mysql_2_0_2($sql, array(':key' => 'version', ':version' => '2.0.2'));

function mysql_2_0_2($sql, $params = array())
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
