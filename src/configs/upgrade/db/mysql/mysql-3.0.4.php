<?php
$status = true;

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}activity` DROP PRIMARY KEY ,
  ADD PRIMARY KEY ( `owner` , `id` ) 
SQL;
$status = $status && mysql_3_0_4($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key;
SQL;
$status = $status && mysql_3_0_4($sql, array(':key' => 'version', ':version' => '3.0.4'));

function mysql_3_0_4($sql, $params = array())
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
