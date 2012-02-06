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

  public function getString($file)
  {
    $file = basename($file);
    $res = $this->db->one("SELECT * FROM `{$this->table}` WHERE `id`=:file", array(':file' => $file));

    if(!$res)
    {
      EpiException::raise(new EpiConfigException("Config file ({$file}) does not exist in db"));
      break; // need to simulate same behavior if exceptions are turned off
    }

    return $res['value'];
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

  public function write($file, $string)
  {
    $file = basename($file);
    $res = $this->db->execute("REPLACE INTO `{$this->table}` (`id`, `value`) VALUES(:file, :value)", array(':file' => $file, ':value' => $string));
    return $res;
  }
}
