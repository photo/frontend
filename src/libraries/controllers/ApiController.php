<?php
/**
  * General controller for API endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiController extends BaseController
{
  /**
    * A diagnostics endpoint used to verify calls are working.
    *
    * @return string Standard JSON envelope 
    */
  public static function hello()
  {
    return self::success('Hello, world!', $_GET);
  }
}
