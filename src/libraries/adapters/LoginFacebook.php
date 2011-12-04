<?php
/**
 * BrowserId implementation
 *
 * This class defines the functionality defined by LoginInterface for BrowserId.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
if(!class_exists('Facebook'))
  require getConfig()->get('paths')->external . '/facebook/facebook.php';

class LoginFacebook implements LoginInterface
{
  private $fb, $appId, $appSecret;
  public function __construct()
  {
    // test app, no one cares
    $this->appId = '232147993517254';
    $this->appSecret = '5816281c38851a25544e316bb1c64084';
    $this->fb = new Facebook(array('appId' => $this->appId, 'secret' => $this->appSecret));
  }

  public function verifyEmail($args)
  {
    $user = $this->fb->getUser();
    if(!$user)
      return false;

    $response = $this->fb->api('/me');
    if(!isset($response['email']))
      return false;

    return $response['email'];
  }
}

