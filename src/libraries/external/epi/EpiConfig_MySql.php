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

  public function load(/*$file, $file, $file, $file...*/)
  {
    $args = func_get_args();
    foreach($args as $file)
    {
      $file = basename($file);
      $res = $this->db->one("SELECT * FROM `{$this->table}` WHERE `id`=:file", array(':file' => $file));

      if(!$res)
      {
        EpiException::raise(new EpiConfigException("Config file ({$file}) does not exist in db"));
        break; // need to simulate same behavior if exceptions are turned off
      }

      $config = parse_ini_string($res['value'], true);
      $this->mergeConfig($config);
    }
  }
}
