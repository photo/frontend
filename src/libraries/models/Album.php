<?php
/**
 * Album model.
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Album extends BaseModel
{
  /*
   * Constructor
   */
  public function __construct($params = null)
  {
    parent::__construct();
    if(isset($params['user']))
      $this->user = $params['user'];
    else
      $this->user = new User;
  }

  public function addElement($albumId, $type, $ids)
  {
    if(!is_array($ids))
      $ids = (array)explode(',', $ids);

    return $this->db->postAlbumAdd($albumId, $type, $ids);
  }

  /*
   * Adjust the counters on a album when the permission of an element changes
   *
   * @param array $albums An array of albums (strings get converted)
   * @param int $permission Permission of 1 or 0
   */
  public function adjustCounters($albums, $permission)
  {
    if(!is_array($albums))
      $albums = (array)explode(',', $albums);

    // if being marked public then increment the public count (private is already being tracked)
    // if being marked private then derement the public count
    $value = $permission == 1 ? 1 : -1;
    $this->db->postAlbumsIncrementer($albums, $value);
  }

  public function create($params)
  {
    $params = $this->whitelistParams($params);
    $params['owner'] = $this->owner;
    $params['actor'] = $this->getActor();
    $id = $this->user->getNextId('album');
    if($id === false)
    {
      $this->logger->crit('Could not fetch next album ID');
      return false;
    }

    $res = $this->db->putAlbum($id, $params);
    if(!$res)
      return false;

    return $id;
  }

  public function delete($id)
  {
    return $this->db->deleteAlbum($id);
  }

  public function getAlbum($id, $includeElements = true, $email = null)
  {
    if($email === null)
      $email = $this->user->getEmailAddress();
    $album = $this->db->getAlbum($id, $email);
    if(!$album)
      return false;

    if(!$this->user->isAdmin())
    {
      if(!$this->isAlbumCoverVisible($album))
        $album['cover'] = null;
    }

    if($includeElements)
      $album['photos'] = $this->db->getAlbumElements($id);

    return $album;
  } 

  public function getAlbumByName($name, $email = null)
  {
    if($email === null)
      $email = $this->user->getEmailAddress();

    $album = $this->db->getAlbumByName($name, $email);
    if(!$album)
      return false;

    if(!$this->user->isAdmin())
    {
      if(!$this->isAlbumCoverVisible($album))
        $album['cover'] = null;
    }

    return $album;
  }

  public function getAlbums($email = null, $limit = null, $offset = null)
  {
    if($email === null)
      $email = $this->user->getEmailAddress();
    
    $albums = $this->db->getAlbums($email, $limit, $offset);
    if($albums === false)
      return false;
    
    if(!$this->user->isAdmin())
    {
      foreach($albums as $key => $alb)
      {
        // first check if album is empty, else check cover visibility
        if(!$this->isAlbumCoverVisible($alb))
          $albums[$key]['cover'] = null;
      }
    }
    
    return $albums;
  } 

  public function removeElement($albumId, $type, $ids)
  {
    if(!is_array($ids))
      $ids = (array)explode(',', $ids);

    return $this->db->postAlbumRemove($albumId, $type, $ids);
  }

  public function update($id, $params)
  {
    $params = $this->whitelistParams($params);
    return $this->db->postAlbum($id, $params);
  }

  private function isAlbumCoverVisible($album)
  {
    // check permission on the cover photo
    if(isset($album['cover']) && !empty($album['cover']))
    {
      // get photo permission via API call
      $photoResp = $this->api->invoke("/photo/{$album['cover']['id']}/view.json", EpiRoute::httpGet);
      if($photoResp['code'] === 200)
        return true;
    }
    return false;
  }

  private function whitelistParams($params)
  {
    $matches = array('name' => 1,'groups' => 1, 'extra' => 1,'count' => 1,'visible' => 1);
    foreach($params as $key => $val)
    {
      if(!isset($matches[$key]))
        unset($params[$key]);
    }

    return $params;
  }
}
