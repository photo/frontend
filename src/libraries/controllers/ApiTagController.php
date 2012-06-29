<?php
/**
  * Tag controller for API endpoints
  *
  * This controller does much of the dispatching to the Tag controller for all tag requests.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiTagController extends ApiBaseController
{
  private $tag;

  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    $this->tag = new Tag;
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
    $res = $this->tag->delete($tag);
    if($res)
      return $this->noContent('Tag deleted successfully', true);
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
    $tag = $this->tag->sanitize($tag);
    $params = $this->tag->validateParams($_POST);
    $res = getDb()->postTag($tag, $params);
    if($res)
    {
      $tag = $this->api->invoke("/{$this->apiVersion}/tag/{$tag}/view.json", EpiRoute::httpGet);
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
    $tagsFromDb = $this->tag->getTags($filters);
    $tags = array(); // see issue #795 why we don't operate directly on $tagsFromDb

    if(is_array($tagsFromDb))
    {
      foreach($tagsFromDb as $key => $tag)
      {
        if(strlen($tag['id']) === 0)
          continue;
        $tag['count'] = $tag[$tagField];
        $tags[] = $tag;
      }
    }
    return $this->success('Tags for the user', $tags);
  }
}
