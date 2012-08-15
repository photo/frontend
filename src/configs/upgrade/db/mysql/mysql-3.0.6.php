<?php

$status = true;

/* add timestamp to the table credentials */
$sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}resourceMap` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(255) NOT NULL,
    `resource` text NOT NULL,
    `dateCreated` int(11) NOT NULL,
    PRIMARY KEY (`owner`,`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
$status = $status && mysql_3_0_6($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
$status = $status && mysql_3_0_6($sql, array(':key' => 'version', ':version' => '3.0.6'));

function mysql_3_0_6($sql, $params = array())
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
