<?php
/**
  * User controller for HTML endpoints.
  * 
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class UserController extends BaseController
{
  /**
    * Log a user in via mobile.
    *
    * @return void
    */
  public static function loginMobile()
  {
    $response = getApi()->invoke('/user/login/mobile.json', EpiRoute::httpPost);
    $redirect = '/';
    if(isset($_POST['redirect']))
      $redirect = $_POST['redirect'];

    if($response['code'] == 200)
      getRoute()->redirect($redirect);
    else
      getRoute()->redirect(sprintf('%s&%s', $redirect, 'error=1'));
  }

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

  /**
    * Generate a mobiel passphrase.
    *
    * @return void
    */
  public static function mobilePassphrase()
  {
    getAuthentication()->requireAuthentication();
    User::setMobilePassphrase();
    getRoute()->redirect('/user/settings');
  }

  /**
    * User's settings page
    *
    * @return void
    */
  public static function settings()
  {
    getAuthentication()->requireAuthentication();
    $credentials = getApi()->invoke('/oauth/list.json', EpiRoute::httpGet);
    $groups = getApi()->invoke('/groups/list.json', EpiRoute::httpGet);
    $mobilePassphrase = User::getMobilePassphrase();
    if(!empty($mobilePassphrase))
      $mobilePassphrase['minutes'] = ceil(($mobilePassphrase['expiresAt']-time())/60);
    $template = sprintf('%s/settings.php', getConfig()->get('paths')->templates);
    $body = getTemplate()->get($template, array('credentials' => $credentials['result'], 'groups' => $groups['result'], 'mobilePassphrase' => $mobilePassphrase));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'settings'));
  }
}
