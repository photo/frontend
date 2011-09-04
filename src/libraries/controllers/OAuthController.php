<?php
class OAuthController extends BaseController
{
  public static function authorize()
  {
    $callback = $verifier = null;
    $separator = '?';

    if(isset($_GET['oauth_callback']))
    {
      $callback = $_GET['oauth_callback'];
      if(stripos($_GET['oauth_callback'], '?') !== false)
        $separator = '&';
    }

    $callback .= "{$separator}oauth_token=token&oauth_verifier=verifier";
    echo sprintf('<a href="%s">Click here to allow and continue</a>', $callback);
  }

  public static function flow()
  {
    if(!isset($_GET['oauth_token']))
    {
      $ch = curl_init('http://opme/v1/oauth/token/request');
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, array());
      $tok = curl_exec($ch);
      curl_close($ch);
      parse_str($tok);
      $callback = sprintf('http://%s/v1/oauth/flow', $_SERVER['HTTP_HOST']);
      echo sprintf('<a href="http://opme/v1/oauth/authorize?oauth_token=%s&oauth_callback=%s">Get request token</a>', $oauth_token, urlencode($callback));
    }
    else
    {
      $ch = curl_init('http://opme/v1/oauth/token/access');
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, array('oauth_token' => $_GET['oauth_token']));
      $tok = curl_exec($ch);
      curl_close($ch);
      parse_str($tok);
      echo sprintf('You exchanged a request token for an access token which is (%s, %s)', $oauth_token, $oauth_token_secret);
    }
  }

  public static function test()
  {
    $oauth = new Auth();
    $oauth->checkRequest();
  }

  public static function tokenAccess()
  {
    $oauth = new Auth('token','token');
    echo 'oauth_token=token&oauth_token_secret=token';
  }

  public static function tokenRequest()
  {
    $oauth = new Auth('token','token');
    $type = 'unauthorized';
    if(isset($_GET['oauth_token']))
      $type = 'authorized';
    echo "oauth_token=token&type={$type}";
  }
}
