<?php
/**
  * Tag controller for API endpoints
  *
  * This controller does much of the dispatching to the Tag controller for all tag requests.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiTagController extends BaseController
{
  public static function post()
  {
    $res = getDb()->postTag($_POST);
    if($res)
      return self::success('Tag created/updated successfully', $_POST);
    else
      return self::error('Tag could not be created/updated', false);
  }

  public static function tags($filter = array())
  {
    $tags = getDb()->getTags();
    return self::success('Tags for the user', $tags);
  }
}
