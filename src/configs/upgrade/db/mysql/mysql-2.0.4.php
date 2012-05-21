<?php
$status = true;

/* action */
$table = 'action';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD UNIQUE KEY `id` (`id`,`owner`);
SQL;
$status = $status && mysql_2_0_4($sql);

/* credential */
$table = 'credential';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD UNIQUE KEY `id` (`id`,`owner`);
SQL;
$status = $status && mysql_2_0_4($sql);

/* elementGroup */
$table = 'elementGroup';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD UNIQUE KEY `owner` (`owner`,`type`,`element`,`group`);
SQL;
$status = $status && mysql_2_0_4($sql);

/* elementTag */
$table = 'elementTag';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD UNIQUE KEY `id` (`owner`,`type`,`element`,`tag`);
SQL;
$status = $status && mysql_2_0_4($sql);

/* group */
$table = 'group';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD UNIQUE KEY `id` (`id`,`owner`);
SQL;
$status = $status && mysql_2_0_4($sql);

/* groupMember */
$table = 'groupMember';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD UNIQUE KEY `owner` (`owner`,`group`,`email`);
SQL;
$status = $status && mysql_2_0_4($sql);

/* photo */
$table = 'photo';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD UNIQUE KEY `id` (`id`,`owner`);
SQL;
$status = $status && mysql_2_0_4($sql);

/* photoVersion */
$table = 'photoVersion';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD UNIQUE KEY `id` (`id`,`owner`,`key`);
SQL;
$status = $status && mysql_2_0_4($sql);

/* tag */
$table = 'tag';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD UNIQUE KEY `id` (`id`,`owner`);
SQL;
$status = $status && mysql_2_0_4($sql);

/* user */
$table = 'user';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD PRIMARY KEY (`id`);
SQL;
$status = $status && mysql_2_0_4($sql);

/* webhook */
$table = 'webhook';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD UNIQUE KEY `id` (`id`,`owner`);
SQL;
$status = $status && mysql_2_0_4($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key;
SQL;
$status = $status && mysql_2_0_4($sql, array(':key' => 'version', ':version' => '2.0.4'));

function mysql_2_0_4($sql, $params = array())
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
