<?php
/**
  * Album controller for HTML endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class AlbumController extends BaseController
{
  private $pageSize = 8;

  public function __construct()
  {
    parent::__construct();
  }

  public function list_()
  {
    $pageSize = $this->pageSize;
    $page = 1;
    if(isset($_GET['pageSize']))
      $pageSize = (int)$_GET['pageSize'];
    if(isset($_GET['page']))
      $page = (int)$_GET['page'];
    $albumsResp = $this->api->invoke('/albums/list.json', EpiRoute::httpGet, array('_GET' => array('pageSize' => $pageSize, 'page' => $page)));
    $albums = $albumsResp['result'];
    $body = $this->theme->get($this->utility->getTemplate('albums.php'), array('albums' => $albums));
    $this->theme->display($this->utility->getTemplate('template.php'), array('body' => $body, 'page' => 'albums'));
  }
}
