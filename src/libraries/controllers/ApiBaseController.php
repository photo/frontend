<?php
/**
  * API Base controller extended by all other controllers.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ApiBaseController
{
  protected $apiVersion;

  /**
   * Status constants
   */
  const statusError = 500;
  const statusSuccess = 200;
  const statusCreated = 201;
  const statusNoContent = 204;
  const statusForbidden = 403;
  const statusNotFound = 404;
  const statusConflict = 409;

  public function __construct()
  {
    $this->api = getApi();
    $this->config = getConfig()->get();
    $this->plugin = getPlugin();
    $this->route = getRoute();
    $this->session = getSession();
    $this->logger = getLogger();
    $this->template = getTemplate();
    $this->theme = getTheme();
    $this->utility = new Utility;
    $this->url = new Url;

    $this->template->template = $this->template;
    $this->template->config = $this->config;
    $this->template->plugin = $this->plugin;
    $this->template->session = $this->session;
    $this->template->theme = $this->theme;
    $this->template->utility = $this->utility;
    $this->template->url = $this->url;
    $this->template->user = new User;

    $this->apiVersion = Request::getApiVersion();
  }

  /**
    * Created, HTTP 201
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  public function created($message, $result = null)
  {
    return $this->json($message, self::statusCreated, $result);
  }

  /**
    * Conflict, HTTP 409
    * This is used when there's a conflict/duplicate
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  public function conflict($message, $result = null)
  {
    return $this->json($message, self::statusConflict, $result);
  }

  /**
    * No content, HTTP 204
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  public function noContent($message, $result = null)
  {
    return $this->json($message, self::statusNoContent, $result);
  }

  /**
    * Server error, HTTP 500
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  public function error($message, $result = null)
  {
    $errorCode = self::statusError;
    if(!empty($result) && isset($result['code']))
    {
      $errorCode = $result['code'];
      unset($result['code']);
      if(empty($result))
        $result = null;
    }
    return $this->json($message, $errorCode, $result);
  }

  /**
    * Success, HTTP 200
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  public function success($message, $result = null)
  {
    return $this->json($message, self::statusSuccess, $result);
  }

  /**
    * Forbidden, HTTP 403
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  public function forbidden($message, $result = null)
  {
    return $this->json($message, self::statusForbidden, $result);
  }

  /**
    * Not Found, HTTP 404
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  public function notFound($message, $result = null)
  {
    return $this->json($message, self::statusNotFound, $result);
  }

  /**
    * Internal method to enforce standard JSON envelope
    *
    * @param string $message A friendly message to describe the operation
    * @param mixed $result The result with values needed by the caller to take action.
    * @return string Standard JSON envelope
    */
  private function json($message, $code, $result = null)
  {
    $response = array('message' => $message, 'code' => $code, 'result' => $result);

    // if httpCodes is * then always return the HTTP status code
    // if httpCodes matches then we return a HTTP status code
    if(isset($_REQUEST['httpCodes']))
    {
      if($_REQUEST['httpCodes'] === '*')
      {
        $this->putHttpHeader($code);
      }
      else
      {
        $codes = (array)explode(',', $_REQUEST['httpCodes']);
        if(in_array($code, $codes))
          $this->putHttpHeader($code);
      }
    }

    // if a callback is in the request then we JSONP the response
    if(isset($_REQUEST['callback']))
      $response['__callback__'] = $_REQUEST['callback'];

    return $response;
  }

  private function putHttpHeader($code)
  {
    if($this->api->isInvoking())
      return;

    switch($code)
    {
      case '201':
        $header = 'HTTP/1.0 201 Created';
        break;
      case '403':
        $header = 'HTTP/1.0 403 Forbidden';
        break;
      case '404':
        $header = 'HTTP/1.0 404 Not Found';
        break;
      case '409':
        $header = 'HTTP/1.0 409 Conflict';
        break;
      case '500':
        $header = 'HTTP/1.0 500 Internal Server Error';
        break;
      case '200':
      default:
        $header = 'HTTP/1.0 200 OK';
        break;
    }
    header($header);
  }
}
