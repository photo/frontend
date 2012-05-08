<?php
/**
 * Interface for the Login models.
 *
 * This defines the interface for any model that wants to connect to authenticate a user.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
interface LoginInterface
{
  public function __construct();
  public function verifyEmail($args);
}

function getLogin($provider)
{
  switch($provider)
  {
    case 'openphoto':
      return new LoginOpenPhoto;
      break;
    case 'facebook':
      return new LoginFacebook;
      break;
    case 'browserid':
    default:
      return new LoginBrowserId;
      break;
  }
}

