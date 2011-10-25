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
    * Delete a tag in the tag database.
    *
    * @return string Standard JSON envelope 
    */
  public static function delete($tag)
  {
    getAuthentication()->requireAuthentication();
    $res = Tag::delete($tag);
    if($res)
      return self::success('Tag deleted successfully', true);
    else
      return self::error('Tag could not be deleted', false);
  }

  /**
    * Create a tag in the tag database.
    *
    * @return string Standard JSON envelope 
    */
  public static function create()
  {
    getAuthentication()->requireAuthentication();
    $tag = $_POST['tag'];
    unset($_POST['tag']);
    return self::update($tag);
  }

  /**
    * Update a tag in the tag database.
    *
    * @return string Standard JSON envelope 
    */
  public static function update($tag)
  {
    getAuthentication()->requireAuthentication();
    $tag = Tag::sanitize($tag);
    $params = Tag::validateParams($_POST);
    $res = getDb()->postTag($tag, $params);
    if($res)
    {
      $tag = getApi()->invoke("/tag/{$tag}/view.json", EpiRoute::httpGet);
      return self::success('Tag created/updated successfully', $tag['result']);
    }
    else
    {
      return self::error('Tag could not be created/updated', false);
    }
  }

  /**
    * Return all tags.
    *
    * @return string Standard JSON envelope 
    */
  public static function list_()
  {
    $filters = array();
    if(User::isOwner())
      $filters['permission'] = 0;
    $tags = getDb()->getTags($filters);
    return self::success('Tags for the user', $tags);
  }
}
