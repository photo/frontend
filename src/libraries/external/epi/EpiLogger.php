<?php
class EpiLogger
{
  const Info = 'info';
  const Warn = 'warn';
  const Crit = 'crit';

  private $levels = array('info'=>1,'warn'=>1,'crit'=>1);
  private static $employ;

  public function __construct()
  {
    if(self::$employ)
    {
      $this->levels = array();
      foreach(self::$employ as $level)
      {
        $this->levels[$level] = 1;
      }
    }
  }

  public function crit($message, $exception = null)
  {
    if(!isset($this->levels[self::Crit]))
      return;

    $this->log($message, self::Crit, $exception);
  }

  public function info($message, $exception = null)
  {
    if(!isset($this->levels[self::Info]))
      return;

    $this->log($message, self::Info, $exception);
  }

  public function warn($message, $exception = null)
  {
    if(!isset($this->levels[self::Warn]))
      return;

    $this->log($message, self::Warn, $exception);
  }

  public static function employ(/* consts */)
  {
    if(func_num_args() > 0 )
    {
      self::$employ = func_get_args();
    }

    return self::$employ;
  }

  private function parseException($exception)
  {
    return "{file:{$exception->getFile()}, line:{$exception->getLine()}, message:\"{$exception->getMessage()}\", trace:\"{$exception->getTraceAsString()}\"}";
  }

  private function log($description, $severity, $exception=null)
  {
    if($exception instanceof Exception)
      $additional = $this->parseException($exception);
    else
      $additional = '';

    error_log("{severity:{$severity}, description:\"{$description}\", additional:{$additional}}");
  }
}

function getLogger()
{
  static $logger;
  if($logger)
    return $logger;

  $logger = new EpiLogger();
  return $logger;
}
