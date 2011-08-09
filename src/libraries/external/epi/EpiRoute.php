<?php
/**
 * EpiRoute master file
 *
 * This contains the EpiRoute class as wel as the EpiException abstract class
 * @author  Jaisen Mathai <jaisen@jmathai.com>
 * @version 1.0  
 * @package EpiRoute
 */

/**
 * This is the EpiRoute class.
 * @name    EpiRoute
 * @author  Jaisen Mathai <jaisen@jmathai.com>
 * @final
 */
class EpiRoute
{
  private static $instance;
  private $routes = array();
  private $regexes= array();
  private $route = null;
  const routeKey= '__route__';
  const httpGet = 'GET';
  const httpPost= 'POST';

  /**
   * get('/', 'function');
   * @name  get
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $route
   * @param mixed $callback
   */
  public function get($route, $callback, $isApi = false)
  {
    $this->addRoute($route, $callback, self::httpGet, $isApi);
  }

  /**
   * post('/', 'function');
   * @name  post
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $route
   * @param mixed $callback
   */
  public function post($route, $callback, $isApi = false)
  {
    $this->addRoute($route, $callback, self::httpPost, $isApi);
  }
  
  /**
   * NOT YET IMPLEMENTED
   * request('/', 'function', array(EpiRoute::httpGet, EpiRoute::httpPost));
   * @name  request
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $route
   * @param mixed $callback
   */
  /*public function request($route, $callback, $httpMethods = array(self::httpGet, self::httpPost))
  {
  }*/

  /**
   * load('/path/to/file');
   * @name  load
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $file
   */
  public function load($file)
  {
    $file = Epi::getPath('config') . "/{$file}";
    if(!file_exists($file))
    {
      EpiException::raise(new EpiException("Config file ({$file}) does not exist"));
      break; // need to simulate same behavior if exceptions are turned off
    }

    $parsed_array = parse_ini_file($file, true);
    foreach($parsed_array as $route)
    {
      $method = strtolower($route['method']);
      if(isset($route['class']) && isset($route['function']))
        $this->$method($route['path'], array($route['class'], $route['function']));
      elseif(isset($route['function']))
        $this->$method($route['path'], $route['function']);
    }
  }
  
  /**
   * EpiRoute::run($_GET['__route__'], $_['routes']); 
   * @name  run
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $route
   * @param array $routes
   * @method run
   * @static method
   */
  public function run($route = false, $httpMethod = null)
  {
    if($route === false)
      $route = isset($_GET[self::routeKey]) ? $_GET[self::routeKey] : '/';

    if($httpMethod === null)
      $httpMethod = $_SERVER['REQUEST_METHOD'];
    $routeDef = $this->getRoute($route, $httpMethod);

    $response = call_user_func_array($routeDef['callback'], $routeDef['args']);
    if(!$routeDef['postprocess'])
      return $response;
    else
      echo json_encode($response);
  }


  /**
   * EpiRoute::getRoute($route); 
   * @name  getRoute
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $route
   * @method getRoute
   * @static method
   */
  public function getRoute($route = false, $httpMethod = null)
  {
    if($route)
      $this->route = $route;
    else
      $this->route = isset($_GET[self::routeKey]) ? $_GET[self::routeKey] : '/';

    if($httpMethod === null)
      $httpMethod = $_SERVER['REQUEST_METHOD'];

    foreach($this->regexes as $ind => $regex)
    {
      if(preg_match($regex, $this->route, $arguments))
      {
        array_shift($arguments);
        $def = $this->routes[$ind];
        if($httpMethod != $def['httpMethod'])
        {
          continue;
        }
        else if(is_array($def['callback']) && method_exists($def['callback'][0], $def['callback'][1]))
        {
          if(Epi::getSetting('debug'))
            getDebug()->addMessage(__CLASS__, sprintf('Matched %s : %s : %s : %s', $httpMethod, $this->route, json_encode($def['callback']), json_encode($arguments)));
          return array('callback' => $def['callback'], 'args' => $arguments, 'postprocess' => $def['postprocess']);
        }
        else if(function_exists($def['callback']))
        {
          if(Epi::getSetting('debug'))
            getDebug()->addMessage(__CLASS__, sprintf('Matched %s : %s : %s : %s', $httpMethod, $this->route, json_encode($def['callback']), json_encode($arguments)));
          return array('callback' => $def['callback'], 'args' => $arguments, 'postprocess' => $def['postprocess']);
        }

        EpiException::raise(new EpiException('Could not call ' . json_encode($def) . " for route {$regex}"));
      }
    }
    EpiException::raise(new EpiException("Could not find route {$this->route} from {$_SERVER['REQUEST_URI']}"));
  }

  /**
   * EpiRoute::redirect($url); 
   * @name  redirect
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $url
   * @method redirect
   * @static method
   */
  public function redirect($url, $code = null, $offDomain = false)
  {
    $continue = !empty($url);
    if($offDomain === false && preg_match('#^https?://#', $url))
      $continue = false;

    if($continue)
    {
      if($code != null && (int)$code == $code)
        header("Status: {$code}");
      header("Location: {$url}");
      die();
    }
    EpiException::raise(new EpiException("Redirect to {$url} failed"));
  }

  public function route()
  {
    return $this->route;
  }

  /*
   * EpiRoute::getInstance
   */
  public static function getInstance()
  {
    if(self::$instance)
      return self::$instance;

    self::$instance = new EpiRoute;
    return self::$instance;
  }

  /**
   * addRoute('/', 'function', 'GET');
   * @name  addRoute
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $route
   * @param mixed $callback
   * @param mixed $method
   * @param string $callback
   */
  private function addRoute($route, $callback, $method, $postprocess = false)
  {
    $this->routes[] = array('httpMethod' => $method, 'path' => $route, 'callback' => $callback, 'postprocess' => $postprocess);
    $this->regexes[]= "#^{$route}\$#";
    if(Epi::getSetting('debug'))
      getDebug()->addMessage(__CLASS__, sprintf('Found %s : %s : %s', $method, $route, json_encode($callback)));
  }
}

function getRoute()
{
  return EpiRoute::getInstance();
}
