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
    $pluginObj = getPlugin();
    $conf = $pluginObj->loadConf($plugin);
    if(!$conf)
      return self::error('Cannot update settings for a deactivated plugin, try activating first.', false);

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

  public static function updateStatus($plugin, $status)
  {
    $siteConfig = getUserConfig()->getSiteSettings();
    $plugins = (array)explode(',', $siteConfig['plugins']['activePlugins']);
    switch($status)
    {
      case 'activate':
        if(!in_array($plugin, $plugins))
          $plugins[] = $plugin;
        break;
      case 'deactivate';
        if(in_array($plugin, $plugins))
        {
          foreach($plugins as $key => $thisPlugin)
          {
            if($plugin == $thisPlugin)
              unset($plugins[$key]);
          }
        }
        break;
    }
    $siteConfig['plugins']['activePlugins'] = implode(',', $plugins);
    $siteConfigStatus = getUserConfig()->writeSiteSettings($siteConfig);
    if(!$siteConfigStatus)
      return self::error('Could not change status of plugin', false);
    else
      return self::success('Plugin status changed', true);
  }
}
