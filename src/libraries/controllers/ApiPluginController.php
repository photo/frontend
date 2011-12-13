<?php
/**
  * Plugin controller for API endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ApiPluginController extends BaseController
{
  public static function list_()
  {
    getAuthentication()->requireAuthentication();
    $pluginObj = getPlugin();
    $pluginsAll = $pluginObj->getAll();
    $pluginsActive = $pluginObj->getActive();
    $plugins = array();

    foreach($pluginsAll as $p)
      $plugins[] = array('name' => $p, 'conf' => $pluginObj->loadConf($p), 'status' => in_array($p, $pluginsActive) ? 'active' : 'inactive');

    return self::success('Plugins', $plugins);
  }

  public static function update($plugin)
  {
    getAuthentication()->requireAuthentication();
    $params = $_POST;
    if(isset($params['status']))
    {
      // this is where we activate and deactivate
    }

    $pluginObj = getPlugin();
    $conf = $pluginObj->loadConf($plugin);
    foreach($conf as $name => $value)
    {
      if(isset($_POST[$name]))
        $conf[$name] = $_POST[$name];
    }

    $status = $pluginObj->writeConf($plugin, Utility::generateIniString($conf));
    
    if($status)
      return self::success('Plugin updated successfully', $conf);
    else
      return self::error('Could not update plugin', false);
  }
}
