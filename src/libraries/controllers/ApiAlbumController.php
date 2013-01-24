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
        return $this->created('Album created', $albumResp['result']);
    }
    return $this->error('Could not add album', false);
  }

  public function delete($id)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $status = $this->album->delete($id);
    if($status)
      return $this->noContent('Album was deleted', true);

    return $this->error('Could not delete album', false);
  }

  public function form()
  {
    $template = $this->theme->get('partials/album-form.php', array('groups' => $groups));;
    return $this->success('Album form', array('markup' => $template));
  }

  public function list_()
  {
    $pageSize = $this->config->pagination->albums;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    if(isset($_GET['pageSize']))
      $pageSize = (int)$_GET['pageSize'];

    $offset = ($pageSize * $page) - $pageSize;
    // model passes on the email
    $albums = $this->album->getAlbums(null, $pageSize, $offset);
    if($albums === false)
      return $this->error('Could not retrieve albums', false);

    $albumCountKey = $this->user->isAdmin() ? 'countPrivate' : 'countPublic';
    foreach($albums as $key => $val)
    {
      $albums[$key]['count'] = $val[$albumCountKey];
      unset($albums[$key]['countPublic'], $albums[$key]['countPrivate']);
    }

    if(!empty($albums))
    {
      $albums[0]['currentPage'] = intval($page);
      $albums[0]['currentRows'] = count($albums);
      $albums[0]['pageSize'] = intval($pageSize);
      $albums[0]['totalPages'] = !empty($pageSize) ? ceil($albums[0]['totalRows'] / $pageSize) : 0;
    }

    return $this->success('List of albums', $albums);
  }

  public function updateIndex($albumId, $type, $action)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $this->logger->info(sprintf('Calling ApiAlbumController::updateIndex with %s, %s, %s', $albumId, $type, $action));

    if(!isset($_POST['ids']) || empty($_POST['ids']))
      return $this->error('Please provide ids', false);

    switch($action)
    {
      case 'add':
        $resp = $this->album->addElement($albumId, $type, $_POST['ids']);
        if($resp)
        {
          $album = $this->album->getAlbum($albumId, false);
          if(empty($album['cover']))
          {
            $ids = (array)explode(',', $_POST['ids']);
            $id = array_pop($ids);
            if($id)
            {
              $lastPhotoResp = $this->api->invoke("/photo/{$id}/view.json", EpiRoute::httpGet, array('_GET' => array('generate' => 'true', 'returnSizes' => '100x100,100x100xCR,200x200,200x200xCR')));
              if($lastPhotoResp['code'] === 200)
              {
                // TODO: this clobbers anything that was in `extra` (currently nothing)
                $this->album->update($albumId, array('extra' => array('cover' => $lastPhotoResp['result'])));
              }
            }
          }
        }
        break;
      case 'remove':
        $resp = $this->album->removeElement($albumId, $type, $_POST['ids']);
        $album = $this->album->getAlbum($albumId);
        $photoIdsArr = (array)explode(',', $_POST['ids']);
        if(isset($album['cover']['id']) && in_array($album['cover']['id'], $photoIdsArr))
        {
          // TODO: this clobbers anything that was in `extra` (currently nothing)
          $this->album->update($albumId, array('extra' => array('cover' => null)));
        }
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

    $albumResp = $this->api->invoke("/{$this->apiVersion}/album/{$id}/view.json", EpiRoute::httpGet);
    return $this->success('Album {$id} updated', $albumResp['result']);
  }

  public function view($id)
  {
    $includeElements = false;
    if(isset($_GET['includeElements']) && $_GET['includeElements'] == '1')
      $includeElements = true;
    $album = $this->album->getAlbum($id, $includeElements);

    $albumCountKey = $this->user->isAdmin() ? 'countPrivate' : 'countPublic';
    $album['count'] = $album[$albumCountKey];
    unset($album['countPublic'], $album['countPrivate']);

    if($album === false)
      return $this->error('Could not retrieve album', false);
    return $this->success('Album', $album);
  }
}
