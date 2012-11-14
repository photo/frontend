<?php
/**
 * Plugin model.
 *
 * This handles dispatching all plugin actions.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Plugin extends BaseModel
{
  protected $pluginDir, $activePlugins = array(), $pluginInstances = array(), $data = array();

  public function __construct($params = null)
  {
    parent::__construct();
    if(isset($params['config']))
      $this->config = $params['config'];
    else
      $this->config = getConfig()->get();

    if(isset($this->config->paths->plugins))
      $this->pluginDir = $this->config->paths->plugins;
  }

  public function getActive()
  {
    $active = array();
    if(!isset($this->config->plugins))
      return $active;

    $confPlugins = $this->config->plugins;
    if($confPlugins !== null)
      $pluginsFromConf = (array)explode(',', $confPlugins->activePlugins);
    else
      $pluginsFromConf = array();

    $plugins = $this->getAll();
    foreach($plugins as $plugin)
    {
      if(in_array($plugin, $pluginsFromConf))
        $active[] = $plugin;
    }

    // if the configuration requires some plugins we force them here
    if(isset($this->config->plugins->requiredPlugins))
      $active = array_merge($active, (array)explode(',', $this->config->plugins->requiredPlugins));

    return array_unique($active);
  }

  public function getAll()
  {
    if(empty($this->pluginDir) || !is_dir($this->pluginDir))
      return array();

    $dir = dir($this->pluginDir);
    $plugins = array();
    while (($name = $dir->read()) !== false)
    {
      if(is_dir(sprintf('%s/%s', $this->pluginDir, $name)) || substr($name, 0, 1) == '.')
        continue;

      $plugins[] = preg_replace('/Plugin$/', '', basename($name, '.php'));
    }
    sort($plugins);
    return $plugins;
  }

  public function getConfigObj()
  {
    return getConfig();
  }

  public function getData($key = null)
  {
    if($key === null)
      return $this->data;
    elseif(array_key_exists($key, $this->data))
      return $this->data[$key];
    else
      return null;
  }

  public function invoke($action, $params = null)
  {
    $output = '';
    foreach($this->pluginInstances as $instance)
    {
      $output .= (string)$instance->$action($params);
    }

    if($output != '')
      echo $output;
  }

  /* to be used with invokeSingle() */
  public function deferInvocation($action, $params = null)
  {
    $retval = array();
    foreach($this->pluginInstances as $instance)
    {
      $retval[] = array($instance, $action, $params);
    }
    return $retval;
  }

  public function invokeSingle($invocationDef)
  {
    $output = (string)$invocationDef[0]->$invocationDef[1]($invocationDef[2]);
    if($output != '')
      echo $output;
  }

  public function isActive($plugin)
  {
    foreach($this->getActive() as $p)
    {
      if($plugin == $p)
        return true;
    }

    return false;
  }

  public function load()
  {
    $this->registerAll();
    $this->registerRoutes();
    return $this;
  }

  public function loadConf($plugin)
  {
    $inst = $this->getInstance($plugin);
    if(!$inst)
      return null;

    $configObj = $this->getConfigObj();
    $conf = $inst->defineConf();
    if($configObj->exists($confPath = sprintf('%s/plugins/%s.%s.ini', $this->config->paths->userdata, $_SERVER['HTTP_HOST'], $plugin)))
    {
      $parsedConf = parse_ini_string($configObj->getString($confPath), true);
      foreach($conf as $name => $tmp)
      {
        if(isset($parsedConf[$name]))
          $conf[$name] = $parsedConf[$name];
      }
      return $conf;
    }
    return $conf;
  }

  public function setData($key, $value)
  {
    $this->data[$key] = $value;
  }

  public function writeConf($plugin, $string)
  {
    $configObj = $this->getConfigObj();
    $pluginDir = sprintf('%s/plugins', $this->config->paths->userdata);

    if($string !== false)
    {
      $pluginConfFile = sprintf('%s/%s.%s.ini', $pluginDir, $_SERVER['HTTP_HOST'], $plugin);
      $fileCreated = $configObj->write($pluginConfFile, $string) !== false;
      if(!$fileCreated)
        $this->logger->warn(sprintf('Could not create file at %s', $pluginConfFile));

      return $fileCreated;
    }
    return false;
  }

  private function getInstance($plugin)
  {
    foreach($this->pluginInstances as $inst)
    {
      if(get_class($inst) == sprintf('%sPlugin', $plugin))
        return $inst;
    }
    return false;
  }

  private function registerAll()
  {
    if(!empty($this->activePlugins))
      return;

    $this->activePlugins = $this->getActive();
    // we verify in getAll that this file exists
    foreach($this->activePlugins as $plugin)
    {
      require sprintf('%s/%sPlugin.php', $this->pluginDir, $plugin);
      $classname = "{$plugin}Plugin";
      $this->pluginInstances[] = new $classname;
      $this->logger->info(sprintf('Registering plugin %s', $plugin));
    }
  }

  private function registerRoutes()
  {
    foreach($this->pluginInstances as $instance)
    {
      $routes = $instance->defineRoutes();
      if(empty($routes))
        continue;

      foreach($routes as $name => $route)
      {
        // this logic is duplicated in PluginBase::getRouteUrl
        $class = get_class($instance);
        $name = preg_replace('/Plugin$/', '', $class);
        $method = strtolower($route[0]);

        $routePath = sprintf('/plugin/%s(%s)', $name, $route[1]);
        $this->route->$method($routePath, array($class, 'routeHandler'));
      }
    }
  }
}
