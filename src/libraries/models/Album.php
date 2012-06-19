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
      $ids = (array)explode(',', $_POST['ids']);

    return $this->db->postAlbumAdd($albumId, $type, $ids);
  }

  public function create($params)
  {
    $params = $this->whitelistParams($params);
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

    if($includeElements)
      $album['photos'] = $this->db->getAlbumElements($id);

    return $album;
  } 

  public function getAlbums($email = null)
  {
    if($email === null)
      $email = $this->user->getEmailAddress();

    return $this->db->getAlbums($email);
  } 

  public function removeElement($albumId, $type, $ids)
  {
    if(!is_array($ids))
      $ids = (array)explode(',', $_POST['ids']);

    return $this->db->postAlbumRemove($albumId, $type, $ids);
  }

  public function update($id, $params)
  {
    $params = $this->whitelistParams($params);
    return $this->db->postAlbum($id, $params);
  }

  private function whitelistParams($params)
  {
    $matches = array('name' => 1,'groups' => 1, 'extra' => 1,'count' => 1,'permission' => 1);
    foreach($params as $key => $val)
    {
      if(!isset($matches[$key]))
        unset($params[$key]);
    }

    return $params;
  }
}
