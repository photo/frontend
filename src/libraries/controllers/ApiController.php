<?php
/**
  * General controller for API endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiController extends BaseController
{
  /**
    * A diagnostics endpoint used to verify backends are working.
    *
    * @return string Standard JSON envelope
    */
  public static function diagnostics()
  {
    getAuthentication()->requireAuthentication();
    $isOkay = true;
    $fsDiagnostics = getFs()->diagnostics();
    $dbDiagnostics = getDb()->diagnostics();
    foreach(array_merge($fsDiagnostics, $dbDiagnostics) as $diag)
    {
      if($diag['status'] === false)
        $isOkay = false;
    }

    if($isOkay)
      return self::success('Diagnostics PASSED!', array('filesystem' => $fsDiagnostics, 'database' => $dbDiagnostics));
    else
      return self::error('Diagnostics FAILED!', array('filesystem' => $fsDiagnostics, 'database' => $dbDiagnostics));
  }

  /**
    * A diagnostics endpoint used to verify calls are working.
    *
    * @return string Standard JSON envelope
    */
  public static function hello()
  {
    if(isset($_GET['auth']) && !empty($_GET['auth']))
      getAuthentication()->requireAuthentication();

    return self::success('Hello, world!', $_GET);
  }

  /**
    * API to get versions of the source, filesystem and database
    *
    * @return string Standard JSON envelope
    */
  public static function version()
  {
    getAuthentication()->requireAuthentication();
    $systemVersion = getConfig()->get('site')->lastCodeVersion;
    $databaseVersion = getDb()->version();
    $filesystemVersion = '0.0.0';
    return self::success('System versions', array('system' => $systemVersion, 'database' => $databaseVersion, 'filesystem' => $filesystemVersion));
  }
}
