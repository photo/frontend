<?php
$status = true;

$sql = <<<SQL
  ALTER TABLE `{$this->mySqlTablePrefix}shareToken` DROP INDEX `owner` , ADD INDEX `owner` ( `owner` , `type` , `data` ); 
SQL;
$status = $status && mysql_4_0_1($sql);

/** add triggers **/
$sql = <<<SQL
CREATE TRIGGER update_album_counts_on_delete AFTER DELETE ON {$this->mySqlTablePrefix}elementAlbum
FOR EACH ROW
BEGIN
  SET @countPublic=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementAlbum AS ea ON p.id = ea.element WHERE ea.owner=OLD.owner AND ea.album=OLD.album AND p.owner=OLD.owner AND p.permission='1');
  SET @countPrivate=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementAlbum AS ea ON p.id = ea.element WHERE ea.owner=OLD.owner AND ea.album=OLD.album AND p.owner=OLD.owner);
  UPDATE {$this->mySqlTablePrefix}album SET countPublic=@countPublic, countPrivate=@countPrivate WHERE owner=OLD.owner AND id=OLD.album;
END
SQL;
$status = $status && mysql_4_0_1($sql);

$sql = <<<SQL
CREATE TRIGGER update_album_counts_on_insert AFTER INSERT ON {$this->mySqlTablePrefix}elementAlbum
FOR EACH ROW
BEGIN
  SET @countPublic=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementAlbum AS ea ON p.id = ea.element WHERE ea.owner=NEW.owner AND ea.album=NEW.album AND p.owner=NEW.owner AND p.permission='1');
  SET @countPrivate=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementAlbum AS ea ON p.id = ea.element WHERE ea.owner=NEW.owner AND ea.album=NEW.album AND p.owner=NEW.owner);
  UPDATE {$this->mySqlTablePrefix}album SET countPublic=@countPublic, countPrivate=@countPrivate WHERE owner=NEW.owner AND id=NEW.album;
END
SQL;
$status = $status && mysql_4_0_1($sql);

$sql = <<<SQL
CREATE TRIGGER update_tag_counts_on_insert AFTER INSERT ON {$this->mySqlTablePrefix}elementTag
FOR EACH ROW
BEGIN
  SET @countPublic=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementTag AS et ON p.id = et.element WHERE et.owner=NEW.owner AND et.tag=NEW.tag AND p.owner=NEW.owner AND p.permission='1');
  SET @countPrivate=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementTag AS et ON p.id = et.element WHERE et.owner=NEW.owner AND et.tag=NEW.tag AND p.owner=NEW.owner);
  UPDATE {$this->mySqlTablePrefix}tag SET countPublic=@countPublic, countPrivate=@countPrivate WHERE owner=NEW.owner AND id=NEW.tag;
END
SQL;
$status = $status && mysql_4_0_1($sql);

$sql = <<<SQL
CREATE TRIGGER update_tag_counts_on_delete AFTER DELETE ON {$this->mySqlTablePrefix}elementTag
FOR EACH ROW
BEGIN
  SET @countPublic=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementTag AS et ON p.id = et.element WHERE et.owner=OLD.owner AND et.tag=OLD.tag AND p.owner=OLD.owner AND p.permission='1');
  SET @countPrivate=(SELECT COUNT(*) FROM {$this->mySqlTablePrefix}photo AS p INNER JOIN {$this->mySqlTablePrefix}elementTag AS et ON p.id = et.element WHERE et.owner=OLD.owner AND et.tag=OLD.tag AND p.owner=OLD.owner);
  UPDATE {$this->mySqlTablePrefix}tag SET countPublic=@countPublic, countPrivate=@countPrivate WHERE owner=OLD.owner AND id=OLD.tag;
END
SQL;
$status = $status && mysql_4_0_1($sql);

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
$status = $status && mysql_4_0_1($sql, array(':key' => 'version', ':version' => '4.0.1'));


function mysql_4_0_1($sql, $params = array())
{
  try {
    getDatabase()->execute($sql, $params);
    getLogger()->info($sql);
  } catch(Exception $e) {
    getLogger()->crit($e->getMessage());
    return false;
  }
  
  return true;
}

return $status;
