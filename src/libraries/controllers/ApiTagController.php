<?php
/**
  * Tag controller for API endpoints
  *
  * This controller does much of the dispatching to the Tag controller for all tag requests.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiTagController extends ApiBaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
  }

  /**
    * Delete a tag in the tag database.
    *
    * @return string Standard JSON envelope
    */
  public function delete($tag)
  {
    getAuthentication()->requireAuthentication();
    $res = Tag::delete($tag);
    if($res)
      return $this->success('Tag deleted successfully', true);
    else
      return $this->error('Tag could not be deleted', false);
  }

  /**
    * Create a tag in the tag database.
    *
    * @return string Standard JSON envelope
    */
  public function create()
  {
    getAuthentication()->requireAuthentication();
    $tag = $_POST['tag'];
    unset($_POST['tag']);
    return $this->update($tag);
  }

  /**
    * Update a tag in the tag database.
    *
    * @return string Standard JSON envelope
    */
  public function update($tag)
  {
    getAuthentication()->requireAuthentication();
    $tag = Tag::sanitize($tag);
    $params = Tag::validateParams($_POST);
    $res = getDb()->postTag($tag, $params);
    if($res)
    {
      $tag = $this->api->invoke("/tag/{$tag}/view.json", EpiRoute::httpGet);
      return $this->success('Tag created/updated successfully', $tag['result']);
    }
    else
    {
      return $this->error('Tag could not be created/updated', false);
    }
  }

  /**
    * Return all tags.
    *
    * @return string Standard JSON envelope
    */
  public function list_()
  {
    $filters = $_GET;
    unset($filters['__route__']);

    $userObj = new User;
    if($userObj->isOwner())
      $filters['permission'] = 0;

    $tagField = $userObj->isOwner() ? 'countPrivate' : 'countPublic';

    $tags = getDb()->getTags($filters);
    if(is_array($tags))
    {
      foreach($tags as $key => $tag)
      {
        $tags[$key]['count'] = $tag[$tagField];
        unset($tags[$key]['countPublic'], $tags[$key]['countPrivate'], $tags[$key]['owner']);
      }
    }
    return $this->success('Tags for the user', $tags);
  }
}
