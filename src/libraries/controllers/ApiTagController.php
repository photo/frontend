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
    * Create a tag in the tag database.
    *
    * @return string Standard JSON envelope
    */
  public function create()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $tag = $_POST['tag'];
    unset($_POST['tag']);
    $res = $this->update($tag);
    if($res['code'] !== 200)
      return $this->error(sprintf('Could not create tag %s', $tag), false);
    // Here we do not return the Tag object since the count would be 0
    //  and tags with a count of 0 are essentially invisible.
    //  See #987
    return $this->created(sprintf('Tag %s created successfully.', $tag), true);
  }

  /**
    * Delete a tag in the tag database.
    *
    * @return string Standard JSON envelope
    */
  public function delete($tag)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $res = $this->tag->delete($tag);
    if($res)
      return $this->noContent('Tag deleted successfully', true);
    else
      return $this->error('Tag could not be deleted', false);
  }

  /**
    * Update a tag in the tag database.
    *
    * @return string Standard JSON envelope
    */
  public function update($tag)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $tag = $this->tag->sanitize($tag);
    $params = $this->tag->validateParams($_POST);
    $res = $this->tag->update($tag, $params);
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
    * Return a single tag tags.
    *
    * @return string Standard JSON envelope
    */
  public function view($tag)
  {
    $tagFromDb = $this->tag->getTag($tag);
    if($tagFromDb === false)
      return $this->notFound(sprintf('Could not find tag %s', $tag), false);

    return $this->success(sprintf('Successfully returned tag %s', $tag), $tagFromDb);
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
    if($userObj->isAdmin())
      $filters['permission'] = 0;

    $tagField = $userObj->isAdmin() ? 'countPrivate' : 'countPublic';
    $tagsFromDb = $this->tag->getTags($filters);
    $tags = array(); // see issue #795 why we don't operate directly on $tagsFromDb

    if(is_array($tagsFromDb))
    {
      foreach($tagsFromDb as $key => $tag)
      {
        if(strlen($tag['id']) === 0)
          continue;
        $tag['count'] = intval($tag[$tagField]);
        unset($tag['countPrivate'], $tag['countPublic']);
        $tags[] = $tag;
      }
    }
    return $this->success('Tags for the user', $tags);
  }
}
