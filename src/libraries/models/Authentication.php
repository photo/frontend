<?php
/**
  * Authentication model
  *
  * This is the model handles authentication and abstracts between HTTP sessions and OAuth.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class Authentication
{
  /**
    * Checks to see if there are any authentication credentials present in this request
    *
    * @return boolean
    */
  public function isRequestAuthenticated()
  {
    if(User::isLoggedIn())
      return true;
    elseif(getCredential()->isOAuthRequest())
      return true;

    return false;
  }

  public function requireAuthentication($requireOwner = true)
  {
    if(getCredential()->isOAuthRequest())
    {
      if(!getCredential()->checkRequest())
      {
        OPException::raise(new OPAuthorizationOAuthException(getCredential()->getErrorAsString()));
      }
    }
    elseif(!User::isLoggedIn() || ($requireOwner && !User::isOwner()))
    {
      OPException::raise(new OPAuthorizationSessionException());
    }
  }

   /**
    * Check that the crumb is valid
    *
    * @param $crumb the crumb posted to validate
    */
  public function requireCrumb($crumb = null)
  {
    if(getCredential()->isOAuthRequest())
      return;
    elseif($crumb === null && isset($_REQUEST['crumb']))
      $crumb = $_REQUEST['crumb'];

    if(getSession()->get('crumb') != $crumb)
      OPException::raise(new OPAuthorizationException('Crumb does not match'));
  }
}

function getAuthentication()
{
  static $authentication;
  if(!$authentication)
    $authentication = new Authentication;

  return $authentication;
}
