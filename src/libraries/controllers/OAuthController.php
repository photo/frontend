<?php
class OAuthController extends BaseController
{
  public static function authorize()
  {
    // TODO require login
    // TODO require SSL
    $callback = null;
    $separator = '?';

    if(isset($_GET['oauth_callback']))
    {
      $callback = $_GET['oauth_callback'];
      if(stripos($_GET['oauth_callback'], '?') !== false)
        $separator = '&';
    }

    // if an oauth_token is passed then display the approval screen else ask to create a credential
    if(isset($_GET['oauth_token']))
    {
      $consumer = getCredential()->getConsumer($_GET['oauth_token']);
      if(!$consumer)
      {
        // TODO templatize this
        echo sprintf('Could not find consumer for token %s', $_GET['oauth_token']);
      }
      else if($consumer['type'] != Credential::typeUnauthorizedRequest)
      {
        // TODO templatize this
        echo sprintf('This token has been approved or is invalid %s', $_GET['oauth_token']);
      }
      else
      {
        $body = getTheme()->get('oauthApprove.php', array('consumer' => $consumer));
        getTheme()->display('template.php', array('body' => $body, 'page' => 'oauth-approve'));
      }
    }
    else
    {
      $body = getTheme()->get('oauthCreate.php', array('callback' => $callback));
      getTheme()->display('template.php', array('body' => $body, 'page' => 'oauth-create'));
    }
  }

  public static function authorizePost()
  {
    // TODO require login
    // TODO require SSL
    if(isset($_GET['oauth_token']) && !empty($_GET['oauth_token']))
    {
      $token = $_GET['oauth_token'];
      // if an oauth_token exists then the user wants to approve it
      // change the status from unauthorized_request to request
      $token = $_GET['oauth_token'];
      $res = getDb()->postCredential($token, array('type' => Credential::typeRequest));
      if(!$res)
      {
        // TODO templatize this
        echo sprintf('Could not convert this unauthorized request token to a request token %s', $_GET['oauth_token']);
        die();
      }

      $consumer = getDb()->getCredential($token);
      $callback = null;
      $separator = '?';

      if(isset($_GET['oauth_callback']))
      {
        $callback = $_GET['oauth_callback'];
        if(stripos($callback, '?') !== false)
          $separator = '&';
      }
      $callback .= "{$separator}&oauth_token={$token}&oauth_verifier={$consumer['verifier']}";
      // TODO require SSL unless omited in the config
      getRoute()->redirect($callback, null, true);
    }
    else
    {
      // no oauth token so this call is to create a credential
      $clientToken = getCredential()->add($_POST['name'], (array)explode(',', $_POST['permissions']));
      if(!$clientToken)
        getLogger()->warn(sprintf('Could not add credential for: %s', json_encode($_POST)));

      $callback = urlencode($_POST['oauth_callback']);
      getRoute()->redirect("/v1/oauth/authorize?oauth_token={$clientToken}&oauth_callback={$callback}");
    }
  }

  public static function flow()
  {
    if(isset($_GET['oauth_token']))
    {
      $token = $_GET['oauth_token'];
      $verifier = $_GET['oauth_verifier'];
      $ch = curl_init(sprintf('http://%s/v1/oauth/token/access', $_SERVER['HTTP_HOST']));
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, array('oauth_token' => $token, 'oauth_verifier' => $verifier));
      $tok = curl_exec($ch);
      curl_close($ch);
      parse_str($tok);
      setcookie('oauth', $tok);
      echo sprintf('You exchanged a request token for an access token<br><a href="?reloaded=1">Reload to make an OAuth request</a>', $oauth_token, $oauth_token_secret);
    }
    else if(!isset($_GET['reloaded']))
    {
      $callback = sprintf('http://%s/v1/oauth/flow', $_SERVER['HTTP_HOST']);
      echo sprintf('<a href="http://opme/v1/oauth/authorize?oauth_callback=%s">Create a new client id</a>', urlencode($callback));
    }
    else
    {
      try {
        parse_str($_COOKIE['oauth']);
        $consumer = getDb()->getCredential($oauth_token);
        $oauth = new OAuth($oauth_token,$oauth_token_secret,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_AUTHORIZATION);
        $oauth->setToken($user_token,$user_secret);
        $oauth->fetch(sprintf('http://%s/v1/oauth/test?oauth_consumer_key=%s', $_SERVER['HTTP_HOST'], $oauth_token));
        $response_info = $oauth->getLastResponseInfo();
        header("Content-Type: {$response_info["content_type"]}");
        echo $oauth->getLastResponse();
      } catch(OAuthException $E) {
        echo "Exception caught!\n";
        echo "Response: ". $E->lastResponse . "\n";
      }
    }
  }

  public static function test()
  {
    if(getCredential()->checkRequest())
    {
      echo "Good work! This request made a successful OAuth request.";
    }
    else
    {
      echo "Boooo!!!! The OAuth request made FAILED :(.";
    }
  }

  public static function tokenAccess()
  {
    // TODO require login
    // TODO require SSL
    // TODO check oauth_verifier
    $token = $_POST['oauth_token'];
    $verifier = $_POST['oauth_verifier'];
    $consumer = getDb()->getCredential($token);
    if(!$consumer || $consumer['verifier'] != $verifier)
    {
      echo 'oauth_error=could_not_authorize';
    }
    else
    {
      getCredential()->addUserToken($consumer['id'], true);
      $consumer = getDb()->getCredential($token);
      echo "oauth_token={$consumer['id']}&oauth_token_secret={$consumer['client_secret']}&user_token={$consumer['user_token']}&user_secret={$consumer['user_secret']}";
    }
  }

  public static function tokenRequest()
  {
    // TODO require login
    // TODO require SSL
    // Not yet implemented
    $type = 'unauthorized';
    if(isset($_GET['oauth_token']))
    {
      $type = 'authorized';
    }
    echo "oauth_token=token&type={$type}";
  }
}
