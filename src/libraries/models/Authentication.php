<?php
/**
  * Authentication model
  *
  * This is the model handles authentication and abstracts between HTTP sessions and OAuth.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class Authentication
{
  /*
   * Constructor
   */
  public function __construct() { } 

  /**
    * Checks to see if there are any authentication credentials present in this request
    *
    * @return boolean
    */
  public function isRequestAuthenticated()
  {
    $credentialObj = getCredential();
    $userObj = new User;
    if($userObj->isLoggedIn())
      return true;
    elseif($credentialObj->isOAuthRequest())
      return true;

    return false;
  }

  public function requireAuthentication($requireOwner = true)
  {
    $credentialObj = getCredential();
    $userObj = new User;
    if($credentialObj->isOAuthRequest())
    {
      if(!$credentialObj->checkRequest())
      {
        OPException::raise(new OPAuthorizationOAuthException($credentialObj->getErrorAsString()));
      }
    }
    elseif(!$userObj->isLoggedIn() || ($requireOwner && !$userObj->isOwner()))
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
    $credentialObj = getCredential();
    if($credentialObj->isOAuthRequest())
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
