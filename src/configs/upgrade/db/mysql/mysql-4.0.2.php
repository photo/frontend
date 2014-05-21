<?php

$status = true;

$sql = <<<SQL
 ALTER TABLE `{$this->mySqlTablePrefix}activity` ADD `elementId` VARCHAR( 6 ) NOT NULL AFTER `type` 
SQL;

mysql_4_0_2($sql);

$sql = <<<SQL
 ALTER TABLE `{$this->mySqlTablePrefix}album` ADD `dateLastPhotoAdded` INT NOT NULL DEFAULT '0';
SQL;

mysql_4_0_2($sql);
$sql = <<<SQL
 ALTER TABLE `{$this->mySqlTablePrefix}photo` ADD `active` BOOLEAN NOT NULL DEFAULT TRUE ;
SQL;

mysql_4_0_2($sql);
$sql = <<<SQL
 ALTER TABLE `{$this->mySqlTablePrefix}elementAlbum` ADD `active` BOOLEAN NOT NULL DEFAULT TRUE ;
SQL;

mysql_4_0_2($sql);
$sql = <<<SQL
 ALTER TABLE `{$this->mySqlTablePrefix}elementTag` ADD `active` BOOLEAN NOT NULL DEFAULT TRUE ;
SQL;

mysql_4_0_2($sql);

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

// Gh-1279
// We return true here since the only reason to include this alter is in case
//  a user upgraded prior to this patch and we'll run it with the table prefix
//  and fail silently if it doesn't take
return true;
