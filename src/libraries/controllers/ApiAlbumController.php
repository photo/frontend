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
  }

  public function create()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    
  }

  public function list_()
  {
    $albums = $this->album->getAlbums();
    return $this->success('List of albums', $albums);
  }

  public function updateIndex()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    
  }

  public function view($id)
  {
    
  }
}
