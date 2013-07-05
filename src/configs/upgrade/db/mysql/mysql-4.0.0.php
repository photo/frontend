<?php

$status = true;

/* add actor columns */
$tables = array(
  'action','activity','album','albumGroup','credential','elementAlbum','elementGroup','elementTag',
  'group','groupMember','photo','photoVersion','resourceMap','tag','webhook'
);

foreach($tables as $table)
{
  $sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD `actor` VARCHAR( 127 ) NOT NULL AFTER `owner` ;
SQL;
  $status = $status && mysql_4_0_0($sql);

  $sql = <<<SQL
    UPDATE `{$this->mySqlTablePrefix}{$table}` SET `actor`=`owner`;
SQL;
  $status = $status && mysql_4_0_0($sql);

}

$sql = <<<SQL
 ALTER TABLE `{$this->mySqlTablePrefix}album` ADD `countPublic` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `extra` ;
SQL;
mysql_4_0_0($sql);

$sql = <<<SQL
 ALTER TABLE `activity` ADD `{$this->mySqlTablePrefix}elementId` VARCHAR( 6 ) NOT NULL AFTER `type` 
SQL;
mysql_4_0_0($sql);

$sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}relationship` (
    `actor` varchar(127) NOT NULL,
    `follows` varchar(127) NOT NULL,
    `dateCreated` datetime NOT NULL,
    PRIMARY KEY (`actor`,`follows`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
mysql_4_0_0($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}activity` DROP PRIMARY KEY , ADD PRIMARY KEY ( `owner` , `id` ) ;
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}elementAlbum` DROP INDEX `element` ;
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}elementAlbum` ADD INDEX ( `owner` , `album` ) ;
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}album` DROP PRIMARY KEY , ADD PRIMARY KEY ( `owner` , `id` ) ;
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}album` ADD `countPrivate` INT( 10 ) NOT NULL DEFAULT '0' AFTER `countPublic` ;
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}tag` DROP INDEX `id` , ADD UNIQUE `owner` ( `owner` , `id` ) 
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}webhook` DROP INDEX `id` , ADD UNIQUE `owner` ( `owner` , `id` ) 
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}album` DROP `visible` ;
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  DROP TRIGGER IF EXISTS `{$this->mySqlTablePrefix}increment_album_photo_count`;
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  DROP TRIGGER IF EXISTS `{$this->mySqlTablePrefix}decrement_album_photo_count`;
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  CREATE TABLE `{$this->mySqlTablePrefix}shareToken` (
    `id` VARCHAR( 10 ) NOT NULL ,
    `owner` VARCHAR( 127 ) NOT NULL ,
    `actor` VARCHAR( 127 ) NOT NULL ,
    `type` ENUM( 'album', 'photo', 'photos', 'video' ) NOT NULL ,
    `data` VARCHAR( 255 ) NOT NULL ,
    `dateExpires` INT UNSIGNED NOT NULL ,
    PRIMARY KEY ( `owner` , `id` ),
    UNIQUE KEY `owner` (`owner`,`type`,`data`)
  ) ENGINE = InnoDB;
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
$status = $status && mysql_4_0_0($sql, array(':key' => 'version', ':version' => '4.0.0'));

function mysql_4_0_0($sql, $params = array())
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
