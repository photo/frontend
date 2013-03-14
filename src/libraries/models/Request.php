<?php
class Request
{
  private static $latestVersion='v2', $latestMinorVersion = 0, $version = null;

  public static function getApiVersion()
  {
    return self::$version;
  }

  public static function getLatestApiVersion()
  {
    if(self::$latestMinorVersion > 0)
      return sprintf('%s.%d', self::$latestVersion, self::$latestMinorVersion);

    return self::$latestVersion;
  }

  public static function setApiVersion()
  {
    if(self::$version === null && isset($_GET['__route__']))
    {
      preg_match('#^/(v[0-9]+)#', $_GET['__route__'], $matches);
      if(isset($matches[1]))
        self::$version = $matches[1];
      else
        self::$version = self::$latestVersion;
    }
  }
}
