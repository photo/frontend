<?php
class ApiAlbumV1Controller extends ApiAlbumController
{
  public function __construct()
  {
    parent::__construct();
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
    if(isset($albums[0]) && isset($albums[0]['totalRows']))
      unset($albums[0]['totalRows']);

    if($albums === false)
      return $this->error('Could not retrieve albums', false);

    $albumCountKey = $this->user->isAdmin() ? 'countPrivate' : 'countPublic';
    foreach($albums as $key => $val)
    {
      $albums[$key]['count'] = $val[$albumCountKey];
      unset($albums[$key]['countPublic'], $albums[$key]['countPrivate']);
    }

    return $this->success('List of albums', $albums);
  }
}
