<?php
$status = true;

$sql = <<<SQL
  DELETE FROM `{$this->mySqlTablePrefix}photoVersion` WHERE `id` NOT IN (SELECT `id` FROM `{$this->mySqlTablePrefix}photo` WHERE `owner`=`{$this->mySqlTablePrefix}photoVersion`.`owner`);
SQL;
$status = $status && mysql_1_3_4($sql, array());

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
$status = $status && mysql_1_3_4($sql, array(':key' => 'version', ':version' => '1.3.4'));

function mysql_1_3_4($sql, $params = array())
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
