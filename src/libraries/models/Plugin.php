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

  private function getActive()
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

  private function getAll()
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
