<?php
$status = true;
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}photo` ADD `albums` TEXT NULL AFTER `pathBase` ;
SQL;
$status = $status && mysql_2_0_6($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}album` ADD `groups` TEXT NULL AFTER `name` ;
SQL;
$status = $status && mysql_2_0_6($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key;
SQL;
$status = $status && mysql_2_0_6($sql, array(':key' => 'version', ':version' => '2.0.6'));

function mysql_2_0_6($sql, $params = array())
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


