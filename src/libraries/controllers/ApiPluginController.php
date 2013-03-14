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
    $pluginWhitelist = getConfig()->get('plugins')->pluginWhitelist;
    if($pluginWhitelist)
      $pluginWhitelist = (array)explode(',', $pluginWhitelist);

    $plugins = array();
    foreach($pluginsAll as $p)
    {
      if(!$pluginWhitelist || in_array($p, $pluginWhitelist))
        $plugins[] = array('name' => $p, 'conf' => $pluginObj->loadConf($p), 'status' => in_array($p, $pluginsActive) ? 'active' : 'inactive');
    }

    return $this->success('Plugins', $plugins);
  }

  public function update($plugin)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
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
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
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

  public function view($plugin)
  {
    getAuthentication()->requireAuthentication();
    $siteConfig = getUserConfig()->getSiteSettings();
    $plugins = (array)explode(',', $siteConfig['plugins']['activePlugins']);
    if(!in_array($plugin, $plugins))
    {
      $this->logger->warn(sprintf('Tried to call /plugin/%s/view.json on an inactive or non existant plugin', $plugin));
      return $this->error('Could not load plugin', false);
    }

    $pluginObj = getPlugin();
    $conf = $pluginObj->loadConf($plugin);

    $bodyTemplate = sprintf('%s/plugin-form.php', $this->config->paths->templates);
    $body = $this->template->get($bodyTemplate, array('plugin' => $plugin, 'conf' => $conf, 'crumb' => $this->session->get('crumb')));
    return $this->success(sprintf('Form for %s plugin', $plugin), array('markup' => $body));
  }
}
