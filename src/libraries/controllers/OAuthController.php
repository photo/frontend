<?php
class OAuthController extends BaseController
{
  public static function authorize()
  {
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
      $consumer = getDb()->getCredentialByUserToken($_GET['oauth_token']);
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
        $bodyTemplate = sprintf('%s/oauthApprove.php', getConfig()->get('paths')->templates);
        $params = array('consumer' => $consumer);
        $body = getTemplate()->get($bodyTemplate, $params);

        $params = array('body' => $body, 'page' => 'oauth-approve');
        if(Utility::isMobile())
        {
          $template = sprintf('%s/template.php', getConfig()->get('paths')->templates);
          getTemplate()->display($template, $params);
        }
        else
        {
          getTheme()->display('template.php', $params);
        }
      }
    }
    else
    {
      $bodyTemplate = sprintf('%s/oauthCreate.php', getConfig()->get('paths')->templates);
      $params = array('callback' => $callback, 'redirect' => $_SERVER['REQUEST_URI']);
      $params['error'] = isset($_GET['error']) && $_GET['error'] == 1;
      $params['name'] = isset($_GET['name']) ? $_GET['name'] : '';
      $body = getTemplate()->get($bodyTemplate, $params);

      $params = array('body' => $body, 'page' => 'oauth-create');
      if(Utility::isMobile())
      {
        $template = sprintf('%s/template.php', getConfig()->get('paths')->templates);
        getTemplate()->display($template, $params);
      }
      else
      {
        getTheme()->display('template.php', $params);
      }
    }
  }

  public static function authorizePost()
  {
    if(!User::isOwner())
    {
      echo '<h1>You need to be logged in to view this page.</h1><button class="login-click">Login now</button>';
      die();
    }

    if(isset($_GET['oauth_token']) && !empty($_GET['oauth_token']))
    {
      $token = $_GET['oauth_token'];
      // if an oauth_token exists then the user wants to approve it
      // change the status from unauthorized_request to request
      $token = $_GET['oauth_token'];
      $consumer = getDb()->getCredentialByUserToken($token);
      $res = getCredential()->convertToken($consumer['id'], Credential::typeRequest);
      if(!$res)
      {
        // TODO templatize this
        echo sprintf('Could not convert this unauthorized request token to a request token %s', $_GET['oauth_token']);
        die();
      }

      // we have to fetch this again to have the consumer key and secret
      $consumer = getDb()->getCredentialByUserToken($token);
      $callback = null;
      $separator = '?';

      if(isset($_GET['oauth_callback']))
      {
        $callback = $_GET['oauth_callback'];
        if(stripos($callback, '?') !== false)
          $separator = '&';
      }
      $callback .= "{$separator}oauth_consumer_key={$consumer['id']}&oauth_consumer_secret={$consumer['clientSecret']}&oauth_token={$consumer['userToken']}&oauth_token_secret={$consumer['userSecret']}&oauth_verifier={$consumer['verifier']}";
      getRoute()->redirect($callback, null, true);
    }
    elseif(isset($_POST['name']) && !empty($_POST['name']))
    {
      // no oauth token so this call is to create a credential
      // TODO make permissions an array
      $consumerKey = getCredential()->add($_POST['name'], array()/*$_POST['permissions']*/);
      if(!$consumerKey)
      {
        getLogger()->warn(sprintf('Could not add credential for: %s', json_encode($consumerKey)));
        echo sprintf('Could not add credential for: %s', json_encode($consumerKey));
        die();
      }

      $consumer = getDb()->getCredential($consumerKey);
      $callback = urlencode($_GET['oauth_callback']);
      getRoute()->redirect("/v1/oauth/authorize?oauth_token={$consumer['userToken']}&oauth_callback={$callback}");
    }
    else
    {
      // TODO templatize this
      echo sprintf('Could not convert this unauthorized request token to a request token %s', $_GET['oauth_token']);
      die();
    }
  }

  public static function flow()
  {
    if(isset($_GET['oauth_token']))
    {
      $consumerKey = $_GET['oauth_consumer_key'];
      $consumerSecret = $_GET['oauth_consumer_secret'];
      $token = $_GET['oauth_token'];
      $tokenSecret = $_GET['oauth_token_secret'];
      $verifier = $_GET['oauth_verifier'];

      try
      {
        $consumer = getDb()->getCredential($token);
        $oauth = new OAuth($consumerKey,$consumerSecret,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_AUTHORIZATION);
        $oauth->setVersion('1.0a');
        $oauth->setToken($token, $tokenSecret);
        $accessToken = $oauth->getAccessToken(sprintf('%s://%s/v1/oauth/token/access', Utility::getProtocol(false), $_SERVER['HTTP_HOST']), null, $verifier);
        $accessToken['oauth_consumer_key'] = $consumerKey;
        $accessToken['oauth_consumer_secret'] = $consumerSecret;
        setcookie('oauth', http_build_query($accessToken));
        if(!isset($accessToken['oauth_token']) || !isset($accessToken['oauth_token_secret']))
          echo sprintf('Invalid response when getting an access token: %s', http_build_query($accessToken));
        else
          echo sprintf('You exchanged a request token for an access token<br><a href="?reloaded=1">Reload to make an OAuth request</a>', $accessToken['oauth_token'], $accessToken['oauth_token_secret']);
      }
      catch(OAuthException $e)
      {
        $message = OAuthProvider::reportProblem($e);
        getLogger()->info($message);
        OPException::raise(new OPAuthorizationOAuthException($message));
      }
    }
    else if(!isset($_GET['reloaded']))
    {
      $callback = sprintf('%s://%s/v1/oauth/flow', Utility::getProtocol(false), $_SERVER['HTTP_HOST']);
      $name = isset($_GET['name']) ? $_GET['name'] : 'OAuth Test Flow';
      echo sprintf('<a href="%s://%s/v1/oauth/authorize?oauth_callback=%s&name=%s">Create a new client id</a>', Utility::getProtocol(false), $_SERVER['HTTP_HOST'], urlencode($callback), urlencode($name));
    }
    else
    {
      try {
        parse_str($_COOKIE['oauth']);
        $consumer = getDb()->getCredential($oauth_token);
        $oauth = new OAuth($oauth_consumer_key,$oauth_consumer_secret,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_AUTHORIZATION);
        $oauth->setToken($oauth_token,$oauth_token_secret);
        $oauth->fetch(sprintf('http://%s/v1/oauth/test?oauth_consumer_key=%s', $_SERVER['HTTP_HOST'], $oauth_consumer_key));
        $response_info = $oauth->getLastResponseInfo();
        header("Content-Type: {$response_info["content_type"]}");
        echo $oauth->getLastResponse();
      } catch(OAuthException $e) {
        $message = OAuthProvider::reportProblem($e);
        getLogger()->info($message);
        OPException::raise(new OPAuthorizationOAuthException($message));
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
      echo sprintf('Boooo!!!! The OAuth request made FAILED :(. Reason: %s', getCredential()->getErrorAsString());
    }
  }

  public static function tokenAccess()
  {
    $oauthParameters = getCredential()->getOAuthParameters();
    $token = $oauthParameters['oauth_token'];
    $verifier = $oauthParameters['oauth_verifier'];
    $consumer = getDb()->getCredentialByUserToken($token);
    if(!$consumer)
    {
      echo 'oauth_error=oauth_invalid_consumer_key';
    }
    elseif($consumer['verifier'] != $verifier)
    {
      echo 'oauth_error=oauth_invalid_verifier';
    }
    elseif($consumer['type'] != Credential::typeRequest)
    {
      echo 'oauth_error=already_exchanged';
    }
    else
    {
      getCredential()->convertToken($consumer['id'], Credential::typeAccess);
      $consumer = getDb()->getCredentialByUserToken($token);
      printf('oauth_token=%s&oauth_token_secret=%s', $consumer['userToken'], $consumer['userSecret']);
    }
  }

  public static function tokenRequest()
  {
    // Not yet implemented
    $type = 'unauthorized';
    if(isset($_GET['oauth_token']))
    {
      $type = 'authorized';
    }
    echo "oauth_token=token&type={$type}";
  }
}
