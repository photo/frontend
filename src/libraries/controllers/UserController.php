<?php
/**
  * User controller for HTML endpoints.
  * 
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class UserController extends BaseController
{
  /**
    * Log a user out.
    *
    * @return void
    */
  public static function logout()
  {
    $res = getApi()->invoke('/user/logout.json', EpiRoute::httpGet);
    getRoute()->redirect('/');
  }

  public static function settings()
  {
    getAuthentication()->requireAuthentication();
    $credentials = getApi()->invoke('/oauth/list.json', EpiRoute::httpGet);
    $groups = getApi()->invoke('/groups/list.json', EpiRoute::httpGet);
    $template = sprintf('%s/settings.php', getConfig()->get('paths')->templates);
    $body = getTemplate()->get($template, array('credentials' => $credentials['result'], 'groups' => $groups['result']));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'settings'));
  }
}
