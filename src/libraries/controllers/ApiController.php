<?php
/**
  * General controller for API endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiController extends ApiBaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
  }

  /**
    * A diagnostics endpoint used to verify backends are working.
    *
    * @return string Standard JSON envelope
    */
  public function diagnostics()
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
      return $this->success('Diagnostics PASSED!', array('version' => $this->config->defaults->currentCodeVersion, 'filesystem' => $fsDiagnostics, 'database' => $dbDiagnostics));
    else
      return $this->error('Diagnostics FAILED!', array('version' => $this->config->defaults->currentCodeVersion, 'filesystem' => $fsDiagnostics, 'database' => $dbDiagnostics));
  }

  /**
    * A diagnostics endpoint used to verify calls are working.
    *
    * @return string Standard JSON envelope
    */
  public function helloV0()
  {
    if(isset($_GET['auth']) && !empty($_GET['auth']))
      getAuthentication()->requireAuthentication();

    return $this->success('Hello, world! This is version zero of the API!', array_merge($_GET, array('api' => $api)));
  }

  /**
    * A diagnostics endpoint used to verify calls are working.
    *
    * @return string Standard JSON envelope
    */
  public function hello()
  {
    if(isset($_GET['auth']) && !empty($_GET['auth']))
      getAuthentication()->requireAuthentication();

    return $this->success('Hello, world!', $_GET);
  }

  /**
    * API to get versions of the source, filesystem and database
    *
    * @return string Standard JSON envelope
    */
  public function version()
  {
    getAuthentication()->requireAuthentication();
    $apiVersion = Request::getLatestApiVersion();
    $systemVersion = getConfig()->get('site')->lastCodeVersion;
    $databaseVersion = getDb()->version();
    $databaseType = getDb()->identity();
    $filesystemVersion = '0.0.0';
    $filesystemType = getFs()->identity();
    return $this->success('System versions', array('api' => $apiVersion, 'system' => $systemVersion, 'database' => $databaseVersion, 'databaseType' => $databaseType,
      'filesystem' => $filesystemVersion, 'filesystemType' => $filesystemType));
  }
}
