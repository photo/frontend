<?php
$owner = getConfig()->get('user')->email;

/*********** group table ***********/
$sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}group` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(255) NOT NULL,
    `appId` varchar(255) DEFAULT NULL,
    `name` varchar(255) DEFAULT NULL,
    `permission` tinyint(4) NOT NULL COMMENT 'Bitmask of permissions',
    UNIQUE KEY `id` (`id`,`owner`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
mysql_1_2_1($sql);
/*********** // group table ***********/


/*********** action/credential/group/photo table ***********/
foreach(array('action','credential','group','photo','photoVersion','tag','webhook') as $table)
{
  // DROP INDEXes
  $sql = <<<SQL
    ALTER TABLE {$this->mySqlTablePrefix}{$table} DROP PRIMARY KEY
SQL;
  mysql_1_2_1($sql);
  //
  $sql = <<<SQL
    ALTER TABLE {$this->mySqlTablePrefix}{$table} DROP INDEX id
SQL;
  mysql_1_2_1($sql);


  // ALTER TABLE to add owner column to {$table} table
  $sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD `owner` VARCHAR( 255 ) NOT NULL AFTER `id`
SQL;
  mysql_1_2_1($sql);

  if($table != 'photoVersion')
  {
    // CREATE INDEX
    $sql = <<<SQL
      ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD UNIQUE (
      `id` ,
      `owner`
      )
SQL;
    mysql_1_2_1($sql);
  }

  // UPDATE owner
  $sql = <<<SQL
    UPDATE `{$this->mySqlTablePrefix}{$table}` SET owner=:owner
SQL;
  mysql_1_2_1($sql, array(':owner' => $owner));
}
/*********** // action/credential/group/photo table ***********/

/*********** element tables ***********/
$sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}elementGroup` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `owner` varchar(255) NOT NULL,
    `type` enum('photo') NOT NULL,
    `element` varchar(6) NOT NULL,
    `group` varchar(6) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `owner` (`owner`,`type`,`element`,`group`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
SQL;
mysql_1_2_1($sql);

$sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}elementTag` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `owner` varchar(255) NOT NULL,
    `type` enum('photo') NOT NULL,
    `element` varchar(6) NOT NULL DEFAULT 'photo',
    `tag` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`owner`,`type`,`element`,`tag`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tag mapping table for photos (and videos in the future)'
SQL;
mysql_1_2_1($sql);
/*********** // element tables ***********/

/*********** group tables ***********/
$sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}groupMember` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `owner` varchar(255) NOT NULL,
    `group` varchar(6) NOT NULL,
    `email` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `owner` (`owner`,`group`,`email`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8
SQL;
mysql_1_2_1($sql);

// migrate existing groups into the groupMember table
$groups = getDatabase()->all("SELECT * FROM `{$this->mySqlTablePrefix}group`");
foreach($groups as $group)
{
  if(empty($group['members']))
    continue;
  $members = (array)explode(',', $group['members']);
  foreach($members as $member)
  {
    $sql = <<<SQL
    INSERT INTO {$this->mySqlTablePrefix}groupMember(`owner`, `group`, `email`)
    VALUES(:owner, :group, :email)
SQL;
    mysql_1_2_1($sql, array(':owner' => $owner, ':group' => $group['id'], ':email' => $member));
  }
}

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}group` DROP `members`
SQL;
mysql_1_2_1($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}group` ADD `permission` TINYINT NOT NULL AFTER `name` 
SQL;
mysql_1_2_1($sql);
/*********** // group tables ***********/

/*********** photo tables ***********/
// migrate tags and groups
$photos = getDatabase()->all("SELECT * FROM `{$this->mySqlTablePrefix}photo`");
foreach($photos as $photo)
{
  // groups
  if(!empty($photo['groups']))
  {
    $groups = (array)explode(',', $photo['groups']);
    foreach($groups as $group)
    {
      $sql = <<<SQL
      INSERT INTO {$this->mySqlTablePrefix}elementGroup(`owner`, `type`, `element`, `group`)
      VALUES(:owner, 'photo', :element, :group)
SQL;
      mysql_1_2_1($sql, array(':owner' => $owner, ':element' => $photo['id'], ':group' => $group));
    }
  }

  // tags
  if(!empty($photo['tags']))
  {
    $tags = (array)explode(',', $photo['tags']);
    foreach($tags as $tag)
    {
      $sql = <<<SQL
      INSERT INTO {$this->mySqlTablePrefix}elementTag(`owner`, `type`, `element`, `tag`)
      VALUES(:owner, 'photo', :element, :tag)
SQL;
      mysql_1_2_1($sql, array(':owner' => $owner, ':element' => $photo['id'], ':tag' => $tag));
    }
  }
}

// add extra column
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}photo` ADD `extra` TEXT NULL AFTER `height` 
SQL;
mysql_1_2_1($sql);

// drop full text indexes
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}photo` DROP INDEX tags
SQL;
mysql_1_2_1($sql);

// change to innoDB
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}photo` ENGINE = InnoDB 
SQL;
mysql_1_2_1($sql);

// drop photoVersion PK
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}photoVersion` DROP PRIMARY KEY
SQL;
mysql_1_2_1($sql);

// change unique index
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}photoVersion` ADD UNIQUE (
  `id` ,
  `owner` ,
  `key`
  )
SQL;
mysql_1_2_1($sql);
/*********** // photo tables ***********/

/*********** tag tables ***********/
// drop tag PK
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}tag` DROP PRIMARY KEY
SQL;
mysql_1_2_1($sql);

// drop tag PK
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}tag` DROP PRIMARY KEY
SQL;

// drop count column
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}tag` DROP `count`
SQL;
mysql_1_2_1($sql);

// add new count columns
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}tag` ADD `countPrivate` INT NOT NULL DEFAULT '0' AFTER `owner` ,
  ADD `countPublic` INT NOT NULL DEFAULT '0' AFTER `countPrivate` 
SQL;
mysql_1_2_1($sql);

// add extra column
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}tag` ADD `extra` TEXT NULL 
SQL;
mysql_1_2_1($sql);

// update tags
$photos = getDatabase()->all("SELECT * FROM `{$this->mySqlTablePrefix}photo`");
$tags = array();
foreach($photos as $photo)
{
  if(empty($photo['tags']))
    continue;
  $theseTags = (array)explode(',', $photo['tags']);
  $publicIncrement = $photo['permission'] == 0 ? 0 : 1;
  foreach($theseTags as $thisTag)
  {
    if(!isset($tags[$thisTag]))
      $tags[$thisTag] = array('private' => 0, 'public' => 0);
    $tags[$thisTag]['private']++;
    $tags[$thisTag]['public'] += $publicIncrement;
  }
}
foreach($tags as $tag => $counts)
{
  $sql = <<<SQL
    UPDATE `{$this->mySqlTablePrefix}tag` SET countPrivate=:countPrivate, countPublic=:countPublic WHERE id=:id AND owner=:owner
SQL;
  mysql_1_2_1($sql, array(':countPrivate' => $counts['private'], ':countPublic' => $counts['public'], ':id' => $tag, ':owner' => $owner));
}
/*********** // tag tables ***********/

/*********** user tables ***********/
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}user` ADD `extra` TEXT NULL AFTER `id` 
SQL;
mysql_1_2_1($sql);

// update extra column with values from individual columns
$user = getDatabase()->one("SELECT * FROM `{$this->mySqlTablePrefix}user` WHERE `id`='1'");
if($user !== false)
{
  $extra = json_encode(
    array(
      'lastPhotoId' => $user['lastPhotoId'],
      'lastActionId' => $user['lastActionId'],
      'lastWebhookId' => $user['lastWebhookId'],
      'lastGroupId' => $user['lastGroupId']
    )
  );
  $sql = <<<SQL
    UPDATE `{$this->mySqlTablePrefix}user` SET `id`=:owner, `extra`=:extra WHERE `id`='1'
SQL;
  mysql_1_2_1($sql, array(':owner' => $owner, ':extra' => $extra));
}

// drop unused columns
$sql = <<<SQL
  ALTER TABLE `op_user`
    DROP `lastPhotoId`,
    DROP `lastActionId`,
    DROP `lastWebhookId`,
    DROP `lastGroupId`;
SQL;
mysql_1_2_1($sql);
/*********** // user tables ***********/

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
mysql_1_2_1($sql, array(':key' => $owner, ':version' => '1.2.1'));

function mysql_1_2_1($sql, $params = array())
{
  try
  {
    getDatabase()->execute($sql, $params);
    getLogger()->info($sql);
  }
  catch(Exception $e)
  {
    getLogger()->crit($e->getMessage()); 
  }
}

return true;
