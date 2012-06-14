<?php
$status = true;
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}elementAlbum` CHANGE `album` `album` VARCHAR( 6 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL 
SQL;
$status = $status && mysql_2_0_5($sql);

$sql = <<<SQL
  CREATE TABLE `{$this->mySqlTablePrefix}albumGroup` (
    `owner` VARCHAR( 127 ) NOT NULL ,
    `album` VARCHAR( 127 ) NOT NULL ,
    `group` VARCHAR( 127 ) NOT NULL ,
    UNIQUE (
    `owner` ,
    `album` ,
    `group`
    )
  ) ENGINE = InnoDB
SQL;
$status = $status && mysql_2_0_5($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key;
SQL;
$status = $status && mysql_2_0_5($sql, array(':key' => 'version', ':version' => '2.0.5'));

function mysql_2_0_5($sql, $params = array())
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

