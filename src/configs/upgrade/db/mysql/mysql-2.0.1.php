<?php

$status = true;

/* action */
$table = 'action';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
$status = $status && mysql_2_0_1($sql);

/* credential */
$table = 'credential';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
$status = $status && mysql_2_0_1($sql);

/* elementGroup */
$table = 'elementGroup';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `owner`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `owner` (`owner`,`type`,`element`,`group`)
SQL;
$status = $status && mysql_2_0_1($sql);

/* elementTag */
$table = 'elementTag';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `owner`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `owner` (`owner`,`type`,`element`,`tag`)
SQL;
$status = $status && mysql_2_0_1($sql);

/* group */
$table = 'group';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
$status = $status && mysql_2_0_1($sql);

/* groupMember */
$table = 'groupMember';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `owner`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `email` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `owner` (`owner`,`group`,`email`)
SQL;
$status = $status && mysql_2_0_1($sql);

/* photo */
$table = 'photo';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
$status = $status && mysql_2_0_1($sql);

/* photoVersion */
$table = 'photoVersion';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
$status = $status && mysql_2_0_1($sql);

/* tag */
$table = 'tag';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `id` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
$status = $status && mysql_2_0_1($sql);

/* user */
$table = 'user';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP PRIMARY KEY
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `id` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` PRIMARY KEY (`id`)
SQL;
$status = $status && mysql_2_0_1($sql);

/* webhook */
$table = 'webhook';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `id` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
$status = $status && mysql_2_0_1($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
$status = $status && mysql_2_0_1($sql, array(':key' => 'version', ':version' => '2.0.1'));

function mysql_2_0_1($sql, $params = array())
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
