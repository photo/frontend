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
  private $isActive, $fb, $appId, $appSecret;
  public function __construct()
  {
    // requires the FacebookConnect plugin to be enabled

    $this->isActive = getPlugin()->isActive('FacebookConnect');
    if($this->isActive)
    {
      $conf = getPlugin()->loadConf('FacebookConnect');
      $this->appId = '232147993517254';
      $this->appSecret = '5816281c38851a25544e316bb1c64084';
      $this->fb = new Facebook(array('appId' => $this->appId, 'secret' => $this->appSecret));
    }
  }

  public function verifyEmail($args)
  {
    if(!$this->isActive)
    {
      getLogger()->crit('The FacebookConnect plugin is not active and needs to be for the Facebook Login adapter.');
      return false;
    }

    $user = $this->fb->getUser();
    if(!$user)
      return false;

    $response = $this->fb->api('/me');
    if(!isset($response['email']))
      return false;

    return $response['email'];
  }
}

