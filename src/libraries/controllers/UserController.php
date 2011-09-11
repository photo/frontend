<?php
/**
  * User controller for HTML endpoints.
  * 
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class UserController extends BaseController
{
  /**
    * Log a user out.
    *
    * @return void
    */
  public static function logout()
  {
    $res = getApi()->invoke('/user/logout.json', EpiRoute::httpGet);
    getRoute()->redirect('/');
  }
}
