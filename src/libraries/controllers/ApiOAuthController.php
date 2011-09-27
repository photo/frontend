<?php
/**
  * OAuth controller for API endpoints.
  * 
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ApiOAuthController extends BaseController
{
  public static function delete($id)
  {
    getAuthentication()->requireAuthentication();
    $res = getDb()->deleteCredential($id);
    if($res)
      return self::success('Oauth credential deleted', true);
    else
      return self::error('Could not delete credential', false);
  }

  public static function list_()
  {
    getAuthentication()->requireAuthentication();
    $res = getDb()->getCredentials();
    if($res)
      return self::success('Oauth Credentials', $res);
    else
      return self::error('Could not retrieve credentials', false);
  }
}
