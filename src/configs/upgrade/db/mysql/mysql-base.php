<?php
try
{
  $utilityObj = new Utility;
  $sql = <<<SQL
  CREATE DATABASE IF NOT EXISTS `{$this->mySqlDb}`
SQL;
  $pdo = new PDO(sprintf('%s:host=%s', 'mysql', $this->mySqlHost), $this->mySqlUser, $utilityObj->decrypt($this->mySqlPassword));
  $pdo->exec($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}action` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(127) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `appId` varchar(255) DEFAULT NULL,
    `targetId` varchar(255) DEFAULT NULL,
    `targetType` varchar(255) DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `name` varchar(255) DEFAULT NULL,
    `avatar` varchar(255) DEFAULT NULL,
    `website` varchar(255) DEFAULT NULL,
    `targetUrl` varchar(1000) DEFAULT NULL,
    `permalink` varchar(1000) DEFAULT NULL,
    `type` varchar(255) DEFAULT NULL,
    `value` varchar(255) DEFAULT NULL,
    `datePosted` varchar(255) DEFAULT NULL,
    `status` int(11) DEFAULT NULL,
    PRIMARY KEY `owner` (`owner`,`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}activity` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(127) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `appId` varchar(255) NOT NULL,
    `type` varchar(32) NOT NULL,
    `elementId` VARCHAR( 6 ) NOT NULL,
    `data` text NOT NULL,
    `permission` BOOLEAN NOT NULL DEFAULT '0',
    `dateCreated` int(10) unsigned NOT NULL,
    PRIMARY KEY `owner` (`owner`,`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}admin` (
    `key` varchar(255) NOT NULL,
    `value` varchar(255) NOT NULL,
    PRIMARY KEY (`key`),
    UNIQUE KEY `key` (`key`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}album` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(255) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `name` varchar(255) NOT NULL,
    `groups` text,
    `extra` text,
    `countPublic` int(10) unsigned NOT NULL DEFAULT '0',
    `countPrivate` int(10) unsigned NOT NULL DEFAULT '0',
    `dateLastPhotoAdded` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY `owner` (`owner`,`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}albumGroup` (
    `owner` varchar(127) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `album` varchar(127) NOT NULL,
    `group` varchar(127) NOT NULL,
    UNIQUE KEY `owner` (`owner`,`album`,`group`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}config` (
    `id` varchar(255) NOT NULL DEFAULT '',
    `aliasOf` varchar(255) DEFAULT NULL,
    `value` text NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}credential` (
    `id` varchar(30) NOT NULL,
    `owner` varchar(127) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `name` varchar(255) DEFAULT NULL,
    `image` text,
    `clientSecret` varchar(255) DEFAULT NULL,
    `userToken` varchar(255) DEFAULT NULL,
    `userSecret` varchar(255) DEFAULT NULL,
    `permissions` varchar(255) DEFAULT NULL,
    `verifier` varchar(255) DEFAULT NULL,
    `type` varchar(100) NOT NULL,
    `status` int(11) DEFAULT '0',
    `dateCreated` INT(11) DEFAULT NULL,
    PRIMARY KEY `owner` (`owner`,`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}elementAlbum` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `owner` varchar(255) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `type` enum('photo') NOT NULL,
    `element` varchar(6) NOT NULL,
    `album` varchar(6) NOT NULL,
    `order` smallint(11) unsigned NOT NULL DEFAULT '0',
    `active` tinyint(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`owner`,`type`,`element`,`album`),
    INDEX (`owner`,`album`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}elementGroup` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `owner` varchar(127) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `type` enum('photo','album') NOT NULL,
    `element` varchar(6) NOT NULL,
    `group` varchar(6) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `owner` (`owner`,`type`,`element`,`group`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}elementTag` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `owner` varchar(127) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `type` enum('photo') NOT NULL,
    `element` varchar(6) NOT NULL DEFAULT 'photo',
    `tag` varchar(127) NOT NULL,
    `active` tinyint(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`owner`,`type`,`element`,`tag`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tag mapping table for photos (and videos in the future)';
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}group` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(127) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `appId` varchar(255) DEFAULT NULL,
    `name` varchar(255) DEFAULT NULL,
    `permission` tinyint(4) NOT NULL COMMENT 'Bitmask of permissions',
    UNIQUE KEY `id` (`id`,`owner`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}groupMember` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `owner` varchar(127) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `group` varchar(6) NOT NULL,
    `email` varchar(127) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `owner` (`owner`,`group`,`email`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}photo` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(127) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `appId` varchar(255) NOT NULL,
    `host` varchar(255) DEFAULT NULL,
    `title` varchar(255) DEFAULT NULL,
    `description` text,
    `key` varchar(255) DEFAULT NULL,
    `hash` varchar(255) DEFAULT NULL,
    `size` int(11) DEFAULT NULL,
    `width` int(11) DEFAULT NULL,
    `height` int(11) DEFAULT NULL,
    `rotation` enum('0','90','180','270') NOT NULL DEFAULT '0',
    `extra` text,
    `exif` text,
    `latitude` float(10,6) DEFAULT NULL,
    `longitude` float(10,6) DEFAULT NULL,
    `views` int(11) DEFAULT NULL,
    `status` int(11) DEFAULT NULL,
    `permission` int(11) DEFAULT NULL,
    `license` varchar(255) DEFAULT NULL,
    `dateTaken` int(11) DEFAULT NULL,
    `dateTakenDay` int(11) DEFAULT NULL,
    `dateTakenMonth` int(11) DEFAULT NULL,
    `dateTakenYear` int(11) DEFAULT NULL,
    `dateUploaded` int(11) DEFAULT NULL,
    `dateUploadedDay` int(11) DEFAULT NULL,
    `dateUploadedMonth` int(11) DEFAULT NULL,
    `dateUploadedYear` int(11) DEFAULT NULL,
    `dateSortByDay` varchar(14) NOT NULL,
    `filenameOriginal` varchar(255) DEFAULT NULL,
    `pathOriginal` varchar(1000) DEFAULT NULL,
    `pathBase` varchar(1000) DEFAULT NULL,
    `albums` text,
    `groups` text,
    `tags` text,
    `active` tinyint(1) NOT NULL DEFAULT '1',
    UNIQUE KEY `owner` (`owner`,`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}photoVersion` (
    `id` varchar(6) NOT NULL DEFAULT '',
    `owner` varchar(127) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `key` varchar(127) NOT NULL DEFAULT '',
    `path` varchar(1000) DEFAULT NULL,
    UNIQUE KEY `id` (`owner`,`id`,`key`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}relationship` (
    `actor` varchar(127) NOT NULL,
    `follows` varchar(127) NOT NULL,
    `dateCreated` datetime NOT NULL,
    PRIMARY KEY (`actor`,`follows`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}resourceMap` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(255) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `resource` text NOT NULL,
    `dateCreated` int(11) NOT NULL,
    PRIMARY KEY (`owner`,`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE `{$this->mySqlTablePrefix}shareToken` (
    `id` VARCHAR( 10 ) NOT NULL ,
    `owner` VARCHAR( 127 ) NOT NULL ,
    `actor` VARCHAR( 127 ) NOT NULL ,
    `type` ENUM( 'album', 'photo', 'photos', 'video' ) NOT NULL ,
    `data` VARCHAR( 255 ) NOT NULL ,
    `dateExpires` INT UNSIGNED NOT NULL ,
    PRIMARY KEY ( `owner` , `id` ),
    KEY `owner` (`owner`,`type`,`data`)
  ) ENGINE = InnoDB;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}tag` (
    `id` varchar(127) NOT NULL,
    `owner` varchar(127) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `countPublic` int(11) NOT NULL DEFAULT '0',
    `countPrivate` int(11) NOT NULL DEFAULT '0',
    `extra` text NOT NULL,
    UNIQUE KEY `owner` (`owner`,`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}user` (
   `id` varchar(255) NOT NULL COMMENT 'User''s email address',
   `password` varchar(64) NOT NULL,
   `extra` text NOT NULL,
   `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}webhook` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(127) NOT NULL,
    `actor` varchar(127) NOT NULL,
    `appId` varchar(255) DEFAULT NULL,
    `callback` varchar(1000) DEFAULT NULL,
    `topic` varchar(255) DEFAULT NULL,
    UNIQUE KEY `owner` (`owner`,`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  /** add triggers **/
  $sql = <<<SQL
  CREATE TRIGGER update_album_counts_on_delete AFTER DELETE ON {$this->mySqlTablePrefix}elementAlbum
  FOR EACH ROW
  BEGIN
    SET @countPublic=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementAlbum AS ea ON p.id = ea.element WHERE ea.owner=OLD.owner AND ea.album=OLD.album AND p.owner=OLD.owner AND p.permission='1');
    SET @countPrivate=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementAlbum AS ea ON p.id = ea.element WHERE ea.owner=OLD.owner AND ea.album=OLD.album AND p.owner=OLD.owner);
    UPDATE {$this->mySqlTablePrefix}album SET countPublic=@countPublic, countPrivate=@countPrivate WHERE owner=OLD.owner AND id=OLD.album;
  END
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TRIGGER update_album_counts_on_insert AFTER INSERT ON {$this->mySqlTablePrefix}elementAlbum
  FOR EACH ROW
  BEGIN
    SET @countPublic=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementAlbum AS ea ON p.id = ea.element WHERE ea.owner=NEW.owner AND ea.album=NEW.album AND p.owner=NEW.owner AND p.permission='1');
    SET @countPrivate=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementAlbum AS ea ON p.id = ea.element WHERE ea.owner=NEW.owner AND ea.album=NEW.album AND p.owner=NEW.owner);
    UPDATE {$this->mySqlTablePrefix}album SET countPublic=@countPublic, countPrivate=@countPrivate WHERE owner=NEW.owner AND id=NEW.album;
  END
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TRIGGER update_tag_counts_on_insert AFTER INSERT ON {$this->mySqlTablePrefix}elementTag
  FOR EACH ROW
  BEGIN
    SET @countPublic=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementTag AS et ON p.id = et.element WHERE et.owner=NEW.owner AND et.tag=NEW.tag AND p.owner=NEW.owner AND p.permission='1');
    SET @countPrivate=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementTag AS et ON p.id = et.element WHERE et.owner=NEW.owner AND et.tag=NEW.tag AND p.owner=NEW.owner);
  UPDATE {$this->mySqlTablePrefix}tag SET countPublic=@countPublic, countPrivate=@countPrivate WHERE owner=NEW.owner AND id=NEW.tag;
  END
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TRIGGER update_tag_counts_on_delete AFTER DELETE ON {$this->mySqlTablePrefix}elementTag
  FOR EACH ROW
  BEGIN
    SET @countPublic=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementTag AS et ON p.id = et.element WHERE et.owner=OLD.owner AND et.tag=OLD.tag AND p.owner=OLD.owner AND p.permission='1');
    SET @countPrivate=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementTag AS et ON p.id = et.element WHERE et.owner=OLD.owner AND et.tag=OLD.tag AND p.owner=OLD.owner);
    UPDATE {$this->mySqlTablePrefix}tag SET countPublic=@countPublic, countPrivate=@countPrivate WHERE owner=OLD.owner AND id=OLD.tag;
  END
SQL;
  mysql_base($sql);
  
  $sql = <<<SQL
    INSERT INTO `{$this->mySqlTablePrefix}admin` (`key`,`value`) 
    VALUES (:key, :value)
SQL;
  mysql_base($sql, array(':key' => 'version', ':value' => '4.0.2'));

  return true;
}
catch(Exception $e)
{
  getLogger()->crit($e->getMessage());
  return false;
}

function mysql_base($sql, $params = array())
{
  try
  {
    getDatabase()->execute($sql, $params);
    getLogger()->info($sql);
  }
  catch(Exception $e)
  {
    getLogger()->crit($e->getMessage()); 
    throw $e;
  }
}
