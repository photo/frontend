<?php
/**
 * FacebookConnect implementation
 *
 * This class defines the functionality defined by LoginInterface for FacebookConnect.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */

// TODO move this to the constructor so we can test this class better
if(!class_exists('Facebook'))
  require getConfig()->get('paths')->external . '/facebook/facebook.php';

class LoginFacebook implements LoginInterface
{
  private $isActive, $fb, $appId, $appSecret;
  public function __construct()
  {
    // requires the FacebookConnect plugin to be enabled
    $this->isActive = getPlugin()->isActive('FacebookConnect') || getPlugin()->isActive('FacebookConnectHosted');
    if($this->isActive)
    {
      if(getPlugin()->isActive('FacebookConnect'))
        $conf = getPlugin()->loadConf('FacebookConnect');
      else
        $conf = getPlugin()->loadConf('FacebookConnectHosted');
      $this->id = $conf['id'];
      $this->secret = $conf['secret'];
      $this->fb = new Facebook(array('appId' => $this->id, 'secret' => $this->secret));
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

