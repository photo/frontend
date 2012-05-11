<?php
/**
 * Account model.
 *
 * Account specific settings
 * This contains queries not present in the standard repository
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Account extends BaseModel
{
  private $conn;

  public function __construct($params = null)
  {
    parent::__construct();
  }

  public function usage()
  {
    
  }

  private function query($sql, $params)
  {

  }

  private function connect()
  {
    if($this->conn)
      return;    

    //$dsn = sprintf('mysql:dbname=testdb;host=127.0.0.1');
    $dsn = sprintf('%s:%s=%s;host=%s');
    $this->conn = null;
  }
}
