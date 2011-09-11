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
}

function getAuthentication()
{
  static $authentication;
  if(!$authentication)
    $authentication = new Authentication;

  return $authentication;
}
