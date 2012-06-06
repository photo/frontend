<?php
class EpiConfig_MySql extends EpiConfig
{
  private $db, $table;
  public function __construct($params)
  {
    parent::__construct();
    $this->db = EpiDatabase::getInstance('mysql', $params['database'], $params['host'], $params['username'], $params['password']);
    $this->table = $params['table'];
  }

  public function getRecord($file)
  {
    $file = $this->getFilePath($file);
    $res = $this->db->one("SELECT * FROM `{$this->table}` WHERE `id`=:file OR `aliasOf`=:aliasOf", array(':file' => $file, ':aliasOf' => $file));
    if(!$res)
    {
      EpiException::raise(new EpiConfigException("Config file ({$file}) does not exist in db"));
      return; // need to simulate same behavior if exceptions are turned off
    }

    return $res;
  }

  public function getString($file)
  {
    $res = $this->getRecord($file);
    return $res['value'];
  }

  public function exists($file)
  {
    $file = $this->getFilePath($file);
    $res = $this->db->one("SELECT * FROM `{$this->table}` WHERE `id`=:file OR `aliasOf`=:aliasOf", array(':file' => $file, ':aliasOf' => $file));
    return $res !== false;
  }

  public function isAlias($file)
  {
    $file = $this->getFilePath($file);
    $res = $this->db->one("SELECT * FROM `{$this->table}` WHERE `id`=:file OR `aliasOf`=:aliasOf", array(':file' => $file, ':aliasOf' => $file));
    if($res === false)
      return null;

    return $file == $res['aliasOf'];
  }

  public function load(/*$file, $file, $file, $file...*/)
  {
    $args = func_get_args();
    foreach($args as $file)
    {
      $confAsIni = $this->getString($file);
      $config = parse_ini_string($confAsIni, true);
      $this->mergeConfig($config);
    }
  }

  public function search($term, $field = null)
  {
    $res = $this->db->all($sql = "SELECT * FROM `{$this->table}` WHERE `value` LIKE :term", array(':term' => "%{$term}%"));
    foreach($res as $r)
    {
      $cfg = parse_ini_string($r['value'], true);
      $cfg['__id__'] = $r['id'];
      if($field !== null)
      {
        if(is_array($field))
        {
          list($k, $v) = each($field);
          if(isset($cfg[$k][$v]) && $cfg[$k][$v] == $term)
            return $cfg;
        }
        else
        {
          if(isset($cfg[$field]))
            return $cfg;
        }
      }
    }
    return false;
  }

  public function write($file, $string, $aliasOf = null)
  {
    $isAlias = $this->isAlias($file);
    $file = $this->getFilePath($file);
    if($isAlias !== null) // isAlias returns null if the record does not exist
    {
      $params = array(':value' => $string);
      $sql = "UPDATE `{$this->table}` SET `value`=:value ";
      if($aliasOf !== null)
      {
        $sql .= ", `aliasOf`=:aliasOf ";
        $params[':aliasOf'] = $this->getFilePath($aliasOf);
      }
      $params[':file'] = $file;
      if(!$isAlias)
        $sql .= " WHERE `id`=:file";
      else
        $sql .= " WHERE `aliasOf`=:file";
      $res = $this->db->execute($sql, $params);
    }
    else
    {
      $res = $this->db->execute("INSERT INTO `{$this->table}` (`id`, `value`, `aliasOf`) VALUES(:file, :value, :aliasOf)", array(':file' => $file, ':value' => $string, ':aliasOf' => $aliasOf));
    }
    return $res !== false;
  }

  private function getFilePath($file)
  {
    return basename($file);
  }
}
