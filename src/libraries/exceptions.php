<?php
class OPException extends Exception
{
  public static function raise($exception)
  {
    $class = get_class($exception);
    switch($class)
    {
      case 'OPAuthorizationException':
      case 'OPAuthorizationSessionException':
        if(substr($_GET['__route__'], -5) == '.json')
        {
          echo json_encode(BaseController::forbidden('You do not have sufficient permissions to access this page.'));
        }
        else
        {
          $body = getTheme()->get('error403.php');
          getTheme()->display('template.php', array('body' => $body, 'page' => 'error-403'));
        }
        die();
        break;
      case 'OPAuthorizationOAuthException':
        getLogger()->warn($exception->getMessage());
        echo json_encode(BaseController::forbidden($exception->getMessage()));
        die();
        break;
      default:
        getLogger()->warn(sprintf('Uncaught exception (%s:%s): %s', $exception->getFile(), $exception->getLine(), $exception->getMessage()));
        throw $exception;
        break;
    }
  }
}
class OPAuthorizationException extends OPException{}
class OPAuthorizationOAuthException extends OPAuthorizationException{}
class OPAuthorizationSessionException extends OPAuthorizationException{}