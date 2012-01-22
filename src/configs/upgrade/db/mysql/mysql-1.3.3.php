<?php

/* action */
$table = 'action';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
mysql_1_3_3($sql);

/* credential */
$table = 'credential';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
mysql_1_3_3($sql);

/* elementGroup */
$table = 'elementGroup';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `owner`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `owner` (`owner`,`type`,`element`,`group`)
SQL;
mysql_1_3_3($sql);

/* elementTag */
$table = 'elementTag';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `owner`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `owner` (`owner`,`type`,`element`,`tag`)
SQL;
mysql_1_3_3($sql);

/* group */
$table = 'group';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
mysql_1_3_3($sql);

/* groupMember */
$table = 'groupMember';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `owner`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `email` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `owner` (`owner`,`group`,`email`)
SQL;
mysql_1_3_3($sql);

/* photo */
$table = 'photo';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
mysql_1_3_3($sql);

/* photoVersion */
$table = 'photoVersion';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
mysql_1_3_3($sql);

/* tag */
$table = 'tag';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `id` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
mysql_1_3_3($sql);

/* user */
$table = 'user';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP PRIMARY KEY
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `id` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` PRIMARY KEY (`id`)
SQL;
mysql_1_3_3($sql);

/* webhook */
$table = 'webhook';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `id` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UPDATE `owner` VARCHAR(127)
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`)
SQL;
mysql_1_3_3($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
mysql_1_3_3($sql, array(':key' => 'version', ':version' => '1.3.3'));

function mysql_1_3_3($sql, $params = array())
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
