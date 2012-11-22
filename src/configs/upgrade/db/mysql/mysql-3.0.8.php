<?php

$status = true;

/* add timestamp to the table credentials */
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}album` CHANGE `permission` `visible` TINYINT( 1 ) NOT NULL DEFAULT '1';
SQL;
$status = $status && mysql_3_0_8($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
$status = $status && mysql_3_0_8($sql, array(':key' => 'version', ':version' => '3.0.8'));

function mysql_3_0_8($sql, $params = array())
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

