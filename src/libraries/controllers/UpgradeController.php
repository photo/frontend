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
  }
}
