<?php
class BaseController
{
  const statusError = 500;
  const statusSuccess = 200;
  const statusCreated = 202;
  const statusForbidden = 403;
  const statusNotFound = 404;

  // response handlers
  protected static function created($message, $result = null)
  {
    return self::json($message, self::statusCreated, $result);
  }

  protected static function error($message, $result = null)
  {
    return self::json($message, self::statusError, $result);
  }

  protected static function success($message, $result = null)
  {
    return self::json($message, self::statusSuccess, $result);
  }

  protected static function forbidden($message, $result = null)
  {
    return self::json($message, self::statusForbidden, $result);
  }

  protected static function notFound($message, $result = null)
  {
    return self::json($message, self::statusNotFound, $result);
  }

  private static function json($message, $code, $result = null)
  {
    $response = array('message' => $message, 'code' => $code, 'result' => $result);
    return $response;
  }
}
?>
