<?php
class EpiApi
{
  private static $instance;
  private $routes = array();
  private $regexes= array();
  private $invoking = false;

  const internal = 'private';
  const external = 'public';

  /**
   * get('/', 'function');
   * @name  get
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $path
   * @param mixed $callback
   */
  public function get($route, $callback, $visibility = self::internal)
  {
    $this->addRoute($route, $callback, EpiRoute::httpGet);
    if($visibility === self::external)
      getRoute()->get($route, $callback, true);
  }

  public function options($route, $callback, $visibility = self::internal)
  {
    $this->addRoute($route, $callback, EpiRoute::httpOptions);
    if($visibility === self::external)
      getRoute()->options($route, $callback, true);
  }

  public function post($route, $callback, $visibility = self::internal)
  {
    $this->addRoute($route, $callback, EpiRoute::httpPost);
    if($visibility === self::external)
      getRoute()->post($route, $callback, true);
  }

  public function invoke($route, $httpMethod = EpiRoute::httpGet, $params = array())
  {
    $this->invoking = true;
    $routeDef = $this->getRoute($route, $httpMethod);

    // this is ugly but required if internal and external calls are to work
    $tmps = array();
    foreach($params as $type => $value)
    {
      $tmps[$type] = $GLOBALS[$type];
      $GLOBALS[$type] = $value;
    }

    $class = new $routeDef['callback'][0];
    $method = $routeDef['callback'][1];
    $retval = call_user_func_array(array($class, $method), $routeDef['args']);

    // restore sanity
    foreach($tmps as $type => $value)
      $GLOBALS[$type] = $value;

    $this->invoking = false;
    return $retval;
  }

  public function isInvoking()
  {
    return $this->invoking;
  }

  /**
   * EpiApi::getRoute($route);
   * @name  getRoute
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $route
   * @method getRoute
   * @static method
   */
  public function getRoute($route, $httpMethod)
  {
    foreach($this->regexes as $ind => $regex)
    {
      if(preg_match($regex, $route, $arguments))
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
            getDebug()->addMessage(__CLASS__, sprintf('Matched %s : %s : %s : %s', $httpMethod, $route, json_encode($def['callback']), json_encode($arguments)));
          return array('callback' => $def['callback'], 'args' => $arguments, 'postprocess' => true);
        }
        else if(function_exists($def['callback']))
        {
          if(Epi::getSetting('debug'))
            getDebug()->addMessage(__CLASS__, sprintf('Matched %s : %s : %s : %s', $httpMethod, $route, json_encode($def['callback']), json_encode($arguments)));
          return array('callback' => $def['callback'], 'args' => $arguments, 'postprocess' => true);
        }

        EpiException::raise(new EpiException('Could not call ' . json_encode($def) . " for route {$regex}"));
      }
    }
    EpiException::raise(new EpiException("Could not find route ({$route}) from ({$_SERVER['REQUEST_URI']})"));
  }

  /**
   * addRoute('/', 'function', 'GET');
   * @name  addRoute
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $path
   * @param mixed $callback
   * @param mixed $method
   */
  private function addRoute($route, $callback, $method)
  {
    $this->routes[] = array('httpMethod' => $method, 'path' => $route, 'callback' => $callback);
    $this->regexes[]= "#^{$route}\$#";
  }
}

function getApi()
{
  static $api;
  if(!$api)
    $api = new EpiApi();

  return $api;
}
