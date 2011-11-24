<?php
/**
 * BrowserId implementation
 *
 * This class defines the functionality defined by LoginInterface for BrowserId.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class LoginBrowserId implements LoginInterface
{
  private $audience;
  public function __construct()
  {
    $this->audience = $_SERVER['HTTP_HOST'];
  }

  public function verifyEmail($args)
  {
    $ch = curl_init('https://browserid.org/verify');
    $params = array('assertion' => $args['assertion'], 'audience' => $this->audience);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CAINFO, getConfig()->get('paths')->configs.'/ca-bundle.crt');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($ch);
    $response = json_decode($response, 1);
    if(!isset($response['status']) || $response['status'] != 'okay')
      return false;

    return $response['email'];
  }
}
