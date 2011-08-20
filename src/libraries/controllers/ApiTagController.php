<?php
/**
  * Tag controller for API endpoints
  *
  * This controller does much of the dispatching to the Tag controller for all tag requests.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiTagController extends BaseController
{
  /**
    * Update or create a tag in the tag database.
    *
    * @return string Standard JSON envelope 
    */
  public static function post($tag)
  {
    $tag = Tag::sanitize($tag);
    $params = Tag::validateParams($_POST);
    $res = getDb()->postTag($tag, $params);
    if($res)
      return self::success('Tag created/updated successfully', $_POST);
    else
      return self::error('Tag could not be created/updated', false);
  }

  /**
    * Return all tags.
    *
    * @return string Standard JSON envelope 
    */
  public static function tags()
  {
    $tags = getDb()->getTags();
    return self::success('Tags for the user', $tags);
  }
}
