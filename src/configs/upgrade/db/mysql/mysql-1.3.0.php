<?php
$photos = getDatabase()->all("SELECT * FROM `{$this->mySqlTablePrefix}photo`");
$params = array();
foreach($photos as $key => $photo)
{
  if(empty($photo['exif']))
    continue;

  $id = $photo['id'];
  $exif = json_decode($photo['exif'], 1);

  if(isset($exif['latitude']) && !empty($exif['latitude']) && isset($exif['longitude']) && !empty($exif['longitude']))
  {
    $params[$key]['id'] = $photo['id'];
    $params[$key]['owner'] = $photo['owner'];
    $params[$key]['latitude'] = $exif['latitude'];
    $params[$key]['longitude'] = $exif['longitude'];
    unset($exif['latitude'], $exif['longitude']);
    $params[$key]['exif'] = json_encode($exif);
  }
}

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}photo` ADD `latitude` FLOAT( 10, 6 ) NULL AFTER `exif` ,
    ADD `longitude` FLOAT( 10, 6 ) NULL AFTER `latitude` 
SQL;
mysql_1_3_0($sql);

foreach($params as $param)
{
  $sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}photo` SET `latitude`=:latitude, `longitude`=:longitude, `exif`=:exif WHERE `id`=:id AND `owner`=:owner
SQL;
  mysql_1_3_0($sql, array('latitude' => $param['latitude'], 'longitude' => $param['longitude'], 'exif' => $param['exif'], 'id' => $param['id'], 'owner' => $param['owner']));
}

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
mysql_1_3_0($sql, array(':key' => 'version', ':version' => '1.3.0'));


function mysql_1_3_0($sql, $params = array())
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
