<?php
$status = true;

$sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}activity` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(255) NOT NULL,
    `appId` varchar(255) NOT NULL,
    `type` varchar(32) NOT NULL,
    `data` text NOT NULL,
    `permission` int(11) DEFAULT NULL,
    `dateCreated` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`,`owner`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
$status = $status && mysql_1_4_0($sql);

$sql = <<<SQL
  ALTER TABLE  `{$this->mySqlTablePrefix}elementTag` ADD INDEX (  `element` )
SQL;
$status = $status && mysql_1_4_0($sql);

$sql = <<<SQL
  ALTER TABLE  `{$this->mySqlTablePrefix}photo` ADD  `filenameOriginal` VARCHAR( 255 ) NULL AFTER  `dateUploadedYear`
SQL;
$status = $status && mysql_1_4_0($sql);

$sql = <<<SQL
  SELECT `id`, `owner`, `pathOriginal` from `{$this->mySqlTablePrefix}photo`
SQL;
$photos = getDatabase()->all($sql);
foreach($photos as $photo)
{
  $filename = basename($photo['pathOriginal']);
  $filenameOriginal = substr($filename, strpos($filename, '-')+1);
  $sql = <<<SQL
    UPDATE `{$this->mySqlTablePrefix}photo` SET `filenameOriginal`=:filenameOriginal WHERE `owner`=:owner AND `id`=:id
SQL;
  getDatabase()->execute($sql, array(':filenameOriginal' => $filenameOriginal, ':owner' => $photo['owner'], ':id' => $photo['id']));
}

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
$status = $status && mysql_1_4_0($sql, array(':key' => 'version', ':version' => '1.4.0'));


function mysql_1_4_0($sql, $params = array())
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
