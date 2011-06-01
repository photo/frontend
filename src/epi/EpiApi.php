<?php
class EpiApi
{
  private static $instance;
  private $routes = array();
  private $regexes= array();

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

  public function post($route, $callback, $visibility = self::internal)
  {
    $this->addRoute($route, $callback, EpiRoute::httpPost);
    if($visibility === self::external)
      getRoute()->post($route, $callback, true);
  }

  public function invoke($route, $httpMethod = EpiRoute::httpGet)
  {
    $routeDef = getRoute()->getRoute($route, $httpMethod);
    return call_user_func_array($routeDef['callback'], $routeDef['args']);
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
