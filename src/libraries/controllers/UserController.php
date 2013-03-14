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
    * Login page
    *
    * @return void
    */
  public function login()
  {
    $userObj = new User;
    $redirect = '/';
    if(isset($_GET['r']) && strpos($_GET['r'], '/') === 0)
      $redirect = $_GET['r'];
    $body = $this->theme->get('login.php', array('r' => $redirect));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'settings'));
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
      $this->route->redirect($redirect);
    else
      $this->route->redirect(sprintf('%s&%s', $redirect, 'error=1'));
  }

  /**
    * Log a user out.
    *
    * @return void
    */
  public function logout()
  {
    $res = $this->api->invoke('/user/logout.json', EpiRoute::httpGet);
    $this->route->redirect('/');
  }
}
