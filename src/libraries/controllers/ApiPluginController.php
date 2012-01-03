<?php
/**
  * Plugin controller for API endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ApiPluginController extends ApiBaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
  }

  public function list_()
  {
    getAuthentication()->requireAuthentication();
    $pluginObj = getPlugin();
    $pluginsAll = $pluginObj->getAll();
    $pluginsActive = $pluginObj->getActive();
    $plugins = array();

    foreach($pluginsAll as $p)
      $plugins[] = array('name' => $p, 'conf' => $pluginObj->loadConf($p), 'status' => in_array($p, $pluginsActive) ? 'active' : 'inactive');

    return $this->success('Plugins', $plugins);
  }

  public function update($plugin)
  {
    getAuthentication()->requireAuthentication();
    $params = $_POST;
    $pluginObj = getPlugin();
    $conf = $pluginObj->loadConf($plugin);
    if(!$conf)
      return $this->error('Cannot update settings for a deactivated plugin, try activating first.', false);

    foreach($conf as $name => $value)
    {
      if(isset($_POST[$name]))
        $conf[$name] = $_POST[$name];
    }

    $status = $pluginObj->writeConf($plugin, $this->utility->generateIniString($conf));
  
    if($status)
      return $this->success('Plugin updated successfully', $conf);
    else
      return $this->error('Could not update plugin', false);
  }

  public function updateStatus($plugin, $status)
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
      return $this->error('Could not change status of plugin', false);
    else
      return $this->success('Plugin status changed', true);
  }
}
