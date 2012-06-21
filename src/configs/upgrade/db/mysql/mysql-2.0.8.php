<?php
$status = true;

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}elementAlbum` DROP `id` ;
SQL;
$status = $status && mysql_2_0_8($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}elementGroup` DROP `id` ;
SQL;
$status = $status && mysql_2_0_8($sql);

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}elementAlbum` CHANGE `element` `element` VARCHAR( 6 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
SQL;
$status = $status && mysql_2_0_8($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key;
SQL;
$status = $status && mysql_2_0_8($sql, array(':key' => 'version', ':version' => '2.0.8'));

function mysql_2_0_8($sql, $params = array())
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
