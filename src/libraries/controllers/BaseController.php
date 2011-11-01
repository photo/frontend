<?php
/**
  * Base controller extended by all other controllers.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class BaseController
{
  /**
   * Status constants
   */
  const statusError = 500;
  const statusSuccess = 200;
  const statusCreated = 202;
  const statusForbidden = 403;
  const statusNotFound = 404;

  /**
    * Created, HTTP 202
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  public static function created($message, $result = null)
  {
    return self::json($message, self::statusCreated, $result);
  }

  /**
    * Server error, HTTP 500
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  public static function error($message, $result = null)
  {
    return self::json($message, self::statusError, $result);
  }

  /**
    * Success, HTTP 200
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  public static function success($message, $result = null)
  {
    return self::json($message, self::statusSuccess, $result);
  }

  /**
    * Forbidden, HTTP 403
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  public static function forbidden($message, $result = null)
  {
    return self::json($message, self::statusForbidden, $result);
  }

  /**
    * Not Found, HTTP 404
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  public static function notFound($message, $result = null)
  {
    return self::json($message, self::statusNotFound, $result);
  }

  /**
    * Internal method to enforce standard JSON envelope
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  private static function json($message, $code, $result = null)
  {
    $response = array('message' => $message, 'code' => $code, 'result' => $result);
    return $response;
  }
}
?>
