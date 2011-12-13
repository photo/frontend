<?php
/**
 * Plugin model.
 *
 * This handles dispatching all plugin actions.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Plugin
{
  private $pluginDir, $activePlugins = array(), $pluginInstances = array();
  public function __construct()
  {
    $paths = getConfig()->get('paths');
    if(isset($paths->plugins))
      $this->pluginDir = $paths->plugins;
    $this->registerAll();
  }

  public function getActive()
  {
    $active = array();
    $confPlugins = getConfig()->get('plugins');
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
    return $active;
  }

  public function getAll()
  {
    if(empty($this->pluginDir) || !is_dir($this->pluginDir))
      return array();
    $dir = dir($this->pluginDir);
    $plugins = array();
    while (($name = $dir->read()) !== false)
    {
      if(is_dir(sprintf('%s/%s', getConfig()->get('paths')->plugins, $name)) || substr($name, 0, 1) == '.')
        continue;

      $plugins[] = preg_replace('/Plugin$/', '', basename($name, '.php'));
    }
    return $plugins;
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

  public function isActive($plugin)
  {
    foreach($this->getActive() as $p)
    {
      if($plugin == $p)
        return true;
    }

    return false;
  }

  public function loadConf($plugin)
  {
    $inst = $this->getInstance($plugin);
    if(!$inst)
      return null;

    if(file_exists($confPath = sprintf('%s/plugins/%s.%s.ini', getConfig()->get('paths')->userdata, $_SERVER['HTTP_HOST'], $plugin)))
    {
      $conf = $inst->defineConf();
      $parsedConf = parse_ini_file($confPath);
      foreach($conf as $name => $tmp)
      {
        if(isset($parsedConf[$name]))
          $conf[$name] = $parsedConf[$name];
      }
    }
    return $conf;
  }

  public function writeConf($plugin, $string)
  {
    if($string !== false)
      return file_put_contents(sprintf('%s/plugins/%s.%s.ini', getConfig()->get('paths')->userdata, $_SERVER['HTTP_HOST'], $plugin), $string);
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
    }
  }
}

function getPlugin()
{
  static $plugin;
  if($plugin)
    return $plugin;

  $plugin = new Plugin;
  return $plugin;
}
