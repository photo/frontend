<?php
try
{
  $sql = <<<SQL
  CREATE DATABASE IF NOT EXISTS `{$this->mySqlDb}`
SQL;
  $pdo = new PDO(sprintf('%s:host=%s', 'mysql', $this->mySqlHost), $this->mySqlUser, Utility::decrypt($this->mySqlPassword));
  $pdo->exec($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}action` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(127) NOT NULL,
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
    UNIQUE KEY `id` (`id`,`owner`)
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
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}credential` (
    `id` varchar(30) NOT NULL,
    `owner` varchar(127) NOT NULL,
    `name` varchar(255) DEFAULT NULL,
    `image` text,
    `clientSecret` varchar(255) DEFAULT NULL,
    `userToken` varchar(255) DEFAULT NULL,
    `userSecret` varchar(255) DEFAULT NULL,
    `permissions` varchar(255) DEFAULT NULL,
    `verifier` varchar(255) DEFAULT NULL,
    `type` varchar(100) NOT NULL,
    `status` int(11) DEFAULT '0',
    UNIQUE KEY `id` (`id`,`owner`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}elementGroup` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `owner` varchar(127) NOT NULL,
    `type` enum('photo') NOT NULL,
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
    `type` enum('photo') NOT NULL,
    `element` varchar(6) NOT NULL DEFAULT 'photo',
    `tag` varchar(127) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`owner`,`type`,`element`,`tag`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tag mapping table for photos (and videos in the future)';
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}group` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(127) NOT NULL,
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
    `appId` varchar(255) NOT NULL,
    `host` varchar(255) DEFAULT NULL,
    `title` varchar(255) DEFAULT NULL,
    `description` text,
    `key` varchar(255) DEFAULT NULL,
    `hash` varchar(255) DEFAULT NULL,
    `size` int(11) DEFAULT NULL,
    `width` int(11) DEFAULT NULL,
    `height` int(11) DEFAULT NULL,
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
    `pathOriginal` varchar(1000) DEFAULT NULL,
    `pathBase` varchar(1000) DEFAULT NULL,
    `groups` text,
    `tags` text,
    UNIQUE KEY `id` (`id`,`owner`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}photoVersion` (
    `id` varchar(6) NOT NULL DEFAULT '',
    `owner` varchar(127) NOT NULL,
    `key` varchar(127) NOT NULL DEFAULT '',
    `path` varchar(1000) DEFAULT NULL,
    UNIQUE KEY `id` (`id`,`owner`,`key`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}tag` (
    `id` varchar(127) NOT NULL,
    `owner` varchar(127) NOT NULL,
    `countPublic` int(11) NOT NULL DEFAULT '0',
    `countPrivate` int(11) NOT NULL DEFAULT '0',
    `extra` text NOT NULL,
    UNIQUE KEY `id` (`id`,`owner`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}user` (
    `id` varchar(255) NOT NULL COMMENT 'User''s email address',
    `extra` text NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}webhook` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(127) NOT NULL,
    `appId` varchar(255) DEFAULT NULL,
    `callback` varchar(1000) DEFAULT NULL,
    `topic` varchar(255) DEFAULT NULL,
    UNIQUE KEY `id` (`id`,`owner`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
  mysql_base($sql);

  $sql = <<<SQL
    INSERT INTO `{$this->mySqlTablePrefix}admin` (`key`,`value`) 
    VALUES (:key, :value)
SQL;
  mysql_base($sql, array(':key' => 'version', ':value' => '1.3.1'));

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
