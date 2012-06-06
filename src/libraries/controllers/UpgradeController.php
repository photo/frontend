<?php
/**
  * Upgrace controller for HTML endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class UpgradeController extends BaseController
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

  public function upgrade()
  {
    getAuthentication()->requireAuthentication();
    $readmeFiles = getUpgrade()->getUpgradeVersions(array('readme'));
    $readmes = array();
    if(!empty($readmeFiles))
    {
      foreach($readmeFiles as $files)
      {
        foreach($files as $version => $file)
        {
          $readmes[$version] = $this->template->get($file);
        }
      }
    }
    $template = sprintf('%s/upgrade.php', getConfig()->get('paths')->templates);
    $body = $this->template->get($template, array('readmes' => $readmes, 'currentVersion' => getUpgrade()->getCurrentVersion(), 'lastVersion' => getUpgrade()->getLastVersion()));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  public function upgradePost()
  {
    getAuthentication()->requireAuthentication();
    getUpgrade()->performUpgrade();
    $configObj = getConfig();
    // Backwards compatibility
    // TODO remove in 2.0
    $basePath = dirname(Epi::getPath('config'));
    $configFile = sprintf('%s/userdata/configs/%s.ini', $basePath, getenv('HTTP_HOST'));
    if(!file_exists($configFile))
      $configFile = sprintf('%s/generated/%s.ini', Epi::getPath('config'), getenv('HTTP_HOST'));
    $config = $configObj->getString($configFile);
    $config = preg_replace('/lastCodeVersion *= *"\d+\.\d+\.\d+"/', sprintf('lastCodeVersion="%s"', getUpgrade()->getCurrentVersion()), $config);
    $configObj->write($configFile, $config);
    $this->route->redirect('/');
  }
}
