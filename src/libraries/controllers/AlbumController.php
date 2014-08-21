<?php
/**
  * Album controller for HTML endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class AlbumController extends BaseController
{
  public function __construct()
  {
    parent::__construct();
  }

  public function list_()
  {
    $userObj = new User;
    $page = 1;
    $pageSize = null;
    if(isset($_GET['pageSize']))
      $pageSize = (int)$_GET['pageSize'];
    if(isset($_GET['page']))
      $page = (int)$_GET['page'];

    $skipEmpty = $userObj->isAdmin() ? '0' : '1';
    $albumsResp = $this->api->invoke('/albums/list.json', EpiRoute::httpGet, array('_GET' => array('page' => $page, 'pageSize' => $pageSize, 'skipEmpty' => $skipEmpty)));
    $albums = $albumsResp['result'];
    $this->plugin->setData('albums', $albums);
    $this->plugin->setData('page', 'albums');
    $body = $this->theme->get($this->utility->getTemplate('albums.php'), array('albums' => $albums));
    $this->theme->display($this->utility->getTemplate('template.php'), array('body' => $body, 'page' => 'albums'));
  }
}
