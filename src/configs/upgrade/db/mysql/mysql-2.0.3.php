<?php

$status = true;

/* photoVersion */
$table = 'photoVersion';
$sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` DROP KEY `id`;
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` UNIQUE KEY `id` (`id`,`owner`,`key`);
SQL;
$status = $status && mysql_2_0_3($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key;
SQL;
$status = $status && mysql_2_0_3($sql, array(':key' => 'version', ':version' => '2.0.3'));

function mysql_2_0_3($sql, $params = array())
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
