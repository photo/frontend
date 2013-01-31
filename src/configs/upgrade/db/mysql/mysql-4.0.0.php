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
  ALTER TABLE `{$this->mySqlTablePrefix}activity` ADD `permission` BOOLEAN NOT NULL DEFAULT '0' AFTER `data` ;
;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}activity` ADD `elementId` VARCHAR( 6 ) NOT NULL AFTER `type`;
SQL;
$status = $status && mysql_4_0_0($sql);


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
  ALTER TABLE `{$this->mySqlTablePrefix}album` CHANGE `count` `countPublic` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}album` ADD `countPrivate` INT( 10 ) NOT NULL DEFAULT '0' AFTER `countPublic` ;
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
DELIMITER ##
CREATE
TRIGGER {$this->mySqlTablePrefix}update_album_counts_on_insert
AFTER INSERT ON {$this->mySqlTablePrefix}elementAlbum
FOR EACH ROW
BEGIN
  SET @countPublic=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementAlbum AS ea ON p.id = ea.element WHERE ea.owner=NEW.owner AND ea.album=NEW.album AND p.owner=NEW.owner AND p.permission='1');
  SET @countPrivate=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementAlbum AS ea ON p.id = ea.element WHERE ea.owner=NEW.owner AND ea.album=NEW.album AND p.owner=NEW.owner);
  UPDATE {$this->mySqlTablePrefix}album SET countPublic=@countPublic, countPrivate=@countPrivate WHERE owner=NEW.owner AND id=NEW.album;
END##
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
DELIMITER ##
CREATE
TRIGGER {$this->mySqlTablePrefix}update_album_counts_on_delete
AFTER DELETE ON {$this->mySqlTablePrefix}elementAlbum
FOR EACH ROW
BEGIN
  SET @countPublic=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementAlbum AS ea ON p.id = ea.element WHERE ea.owner=OLD.owner AND ea.album=OLD.album AND p.owner=OLD.owner AND p.permission='1');
  SET @countPrivate=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementAlbum AS ea ON p.id = ea.element WHERE ea.owner=OLD.owner AND ea.album=OLD.album AND p.owner=OLD.owner);
  UPDATE {$this->mySqlTablePrefix}album SET countPublic=@countPublic, countPrivate=@countPrivate WHERE owner=OLD.owner AND id=OLD.album;
END##
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
DELIMITER ##
CREATE
TRIGGER {$this->mySqlTablePrefix}update_tag_counts_on_insert
AFTER INSERT ON {$this->mySqlTablePrefix}elementTag
FOR EACH ROW
BEGIN
  SET @countPublic=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementTag AS et ON p.id = et.element WHERE et.owner=NEW.owner AND et.tag=NEW.tag AND p.owner=NEW.owner AND p.permission='1');
  SET @countPrivate=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementTag AS et ON p.id = et.element WHERE et.owner=NEW.owner AND et.tag=NEW.tag AND p.owner=NEW.owner);
  UPDATE tag SET countPublic=@countPublic, countPrivate=@countPrivate WHERE owner=NEW.owner AND id=NEW.tag;
END##
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
DELIMITER ##
CREATE
TRIGGER {$this->mySqlTablePrefix}update_tag_counts_on_delete
AFTER DELETE ON {$this->mySqlTablePrefix}elementTag
FOR EACH ROW
BEGIN
  SET @countPublic=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementTag AS et ON p.id = et.element WHERE et.owner=OLD.owner AND et.tag=OLD.tag AND p.owner=OLD.owner AND p.permission='1');
  SET @countPrivate=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementTag AS et ON p.id = et.element WHERE et.owner=OLD.owner AND et.tag=OLD.tag AND p.owner=OLD.owner);
  UPDATE {$this->mySqlTablePrefix}tag SET countPublic=@countPublic, countPrivate=@countPrivate WHERE owner=OLD.owner AND id=OLD.tag;
END##
SQL;
$status = $status && mysql_4_0_0($sql);

$sql = <<<SQL
  CREATE TABLE `{$this->mySqlTablePrefix}relationship` (
   `actor` varchar(127) NOT NULL,
   `follows` varchar(127) NOT NULL,
   `dateCreated` datetime NOT NULL,
   PRIMARY KEY (`actor`,`follows`)
  ) ENGINE=InnoDB;
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
    PRIMARY KEY ( `owner` , `id` )
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


