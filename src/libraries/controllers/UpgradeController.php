<?php
/**
  * Upgrace controller for HTML endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class UpgradeController extends BaseController
{
  public static function upgrade()
  {
    getAuthentication()->requireAuthentication();
    $template = sprintf('%s/upgrade.php', getConfig()->get('paths')->templates);
    $body = getTemplate()->get($template, array('currentVersion' => getUpgrade()->getCurrentVersion(), 'lastVersion' => getUpgrade()->getLastVersion()));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'setup'));
  }

  public static function upgradePost()
  {
    getAuthentication()->requireAuthentication();
    getUpgrade()->performUpgrade();
    $configFile = sprintf('%s/generated/%s.ini', getConfig()->get('paths')->configs, getenv('HTTP_HOST'));
    $config = file_get_contents($configFile);
    $config = preg_replace('/lastCodeVersion="\d+.\d+.\d+"/', sprintf('lastCodeVersion="%s"', getUpgrade()->getCurrentVersion()), $config);
    file_put_contents($configFile, $config);
    getRoute()->redirect('/');
  }
}
