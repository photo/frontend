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

  public function getAlbums($email = null)
  {
    if($email === null)
      $email = $this->user->getEmailAddress();

    return $this->db->getAlbums($email);
  } 
}
