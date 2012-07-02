<?php
$status = true;

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}photo` ADD `rotation` ENUM( '0', '90', '180', '270' ) NOT NULL DEFAULT '0' AFTER `height` 
SQL;
$status = $status && mysql_3_0_1($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key;
SQL;
$status = $status && mysql_3_0_1($sql, array(':key' => 'version', ':version' => '3.0.1'));

function mysql_3_0_1($sql, $params = array())
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
