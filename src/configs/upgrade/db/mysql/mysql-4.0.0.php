<?php

$status = true;

/* add actor columns */
$tables = array(
  'action','activity','album','albumGroup','credential','elementAlbum','elementGroup','elementTag',
  'group','groupMember','photo','photoVersion','resourceMap','tag','webhook'
);

foreach($tables as $table)
{
  $sql = <<<SQL
    ALTER TABLE `{$this->mySqlTablePrefix}{$table}` ADD `actor` VARCHAR( 127 ) NOT NULL AFTER `owner` ;
SQL;
  $status = $status && mysql_4_0_0($sql);

  $sql = <<<SQL
    UPDATE `{$this->mySqlTablePrefix}{$table}` SET `actor`=`owner`;
SQL;

}

// GH-1024
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}tag` DROP INDEX `id` ,
  ADD UNIQUE `id` ( `owner` , `id` ) ;
SQL;
$status = $status && mysql_4_0_0($sql, array(':key' => 'version', ':version' => '4.0.0'));

// GH-1025
$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}config` ADD INDEX ( `aliasOf` ) ;
SQL;
$status = $status && mysql_4_0_0($sql, array(':key' => 'version', ':version' => '4.0.0'));




$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
$status = $status && mysql_3_0_8($sql, array(':key' => 'version', ':version' => '4.0.0'));

function mysql_4_0_0($sql, $params = array())
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


