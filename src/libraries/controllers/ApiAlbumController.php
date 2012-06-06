<?php
/**
  * Album controller for API endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiAlbumController extends ApiBaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
    $this->album = new Album;
    $this->user = new User;
  }

  public function create()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();

    $albumId = $this->album->create($_POST);
    if($albumId)
    {
      $albumResp = $this->api->invoke("/{$this->apiVersion}/album/{$albumId}/view.json", EpiRoute::httpGet);
      if($albumResp['code'] == 200)
        return $this->success('Album created', $albumResp['result']);
    }
    return $this->error('Could not add album', false);
  }

  public function list_()
  {
    $albums = $this->album->getAlbums();
    if($albums === false)
      return $this->error('Could not retrieve albums', false);
    return $this->success('List of albums', $albums);
  }

  public function updateIndex($albumId, $type, $action)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    
    if(!isset($_POST['ids']) || empty($_POST['ids']))
      return $this->error('Please provide ids', false);

    $cnt = array('success' => 0, 'failure' => 0);
    switch($action)
    {
      case 'add':
        $resp = $this->album->addElement($albumId, $type, $_POST['ids']);
        break;
      case 'remove':
        $resp = $this->album->removeElement($albumId, $type, $_POST['ids']);
        break;
    }

    if(!$resp)
      return $this->error('All items were not updated', false);

    return $this->success('All items updated', true);
  }

  public function update($id)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();

    $status = $this->album->update($id, $_POST);
    if(!$status)
      return $this->error('Could not update album', false);

    return $this->success('Album updated', true);
  }

  public function view($id)
  {
    $album = $this->album->getAlbum($id);
    if($album === false)
      return $this->error('Could not retrieve album', false);
    return $this->success('Album', $album);
  }
}
