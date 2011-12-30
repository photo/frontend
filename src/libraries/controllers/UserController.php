<?php
/**
  * User controller for HTML endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class UserController extends BaseController
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

  /**
    * Log a user in via mobile.
    *
    * @return void
    */
  public function loginMobile()
  {
    $response = $this->api->invoke('/user/login/mobile.json', EpiRoute::httpPost);
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
  public function logout()
  {
    $res = $this->api->invoke('/user/logout.json', EpiRoute::httpGet);
    getRoute()->redirect('/');
  }

  /**
    * Generate a mobiel passphrase.
    *
    * @return void
    */
  public function mobilePassphrase()
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
  public function settings()
  {
    getAuthentication()->requireAuthentication();
    $credentials = $this->api->invoke('/oauth/list.json', EpiRoute::httpGet);
    $groups = $this->api->invoke('/groups/list.json', EpiRoute::httpGet);
    $webhooks = $this->api->invoke('/webhooks/list.json', EpiRoute::httpGet);
    $plugins = $this->api->invoke('/plugins/list.json', EpiRoute::httpGet);
    $mobilePassphrase = User::getMobilePassphrase();
    if(!empty($mobilePassphrase))
      $mobilePassphrase['minutes'] = ceil(($mobilePassphrase['expiresAt']-time())/60);
    $template = sprintf('%s/settings.php', $this->config->paths->templates);
    $body = getTemplate()->get($template, array('crumb' => getSession()->get('crumb'), 'plugins' => $plugins['result'], 'credentials' => $credentials['result'], 'webhooks' => $webhooks['result'], 'groups' => $groups['result'], 'mobilePassphrase' => $mobilePassphrase));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'settings'));
  }
}
