<?php

$status = true;

$sql = <<<SQL
 ALTER TABLE `{$this->mySqlTablePrefix}activity` ADD `elementId` VARCHAR( 6 ) NOT NULL AFTER `type` 
SQL;

$status = $status && mysql_4_0_2($sql);

$sql = <<<SQL
 ALTER TABLE `{$this->mySqlTablePrefix}album` ADD `dateLastPhotoAdded` INT NOT NULL DEFAULT '0';
SQL;

$status = $status && mysql_4_0_2($sql);

$sql = <<<SQL
 ALTER TABLE `{$this->mySqlTablePrefix}photo` ADD `active` BOOLEAN NOT NULL DEFAULT TRUE ;
SQL;

$status = $status && mysql_4_0_2($sql);

$sql = <<<SQL
 ALTER TABLE `{$this->mySqlTablePrefix}elementAlbum` ADD `active` BOOLEAN NOT NULL DEFAULT TRUE ;
SQL;

$status = $status && mysql_4_0_2($sql);

$sql = <<<SQL
 ALTER TABLE `{$this->mySqlTablePrefix}elementTag` ADD `active` BOOLEAN NOT NULL DEFAULT TRUE ;
SQL;

$status = $status && mysql_4_0_2($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key;
SQL;
$status = $status && mysql_4_0_2($sql, array(':key' => 'version', ':version' => '4.0.2'));

function mysql_4_0_2($sql, $params = array())
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
