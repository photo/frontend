<?php
/**
  * Manage controller for HTML endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ManageController extends BaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
    $this->photo = new Photo;
    $this->theme->setTheme('beisel2.0');
  }

  public function home()
  {
    $photosApiParams = array('_GET' => array_merge($_GET, array('returnSizes' => '160x160xCR', 'pageSize' => 18)));
    $photosResp = $this->api->invoke('/photos/list.json', EpiRoute::httpGet, $photosApiParams);
    $photos = $photosResp['result'];

    $pages = array('pages' => array());
    if(!empty($photos))
    {
      $pages['pages'] = $this->utility->getPaginationParams($photos[0]['currentPage'], $photos[0]['totalPages'], $this->config->pagination->pagesToDisplay);
      $pages['currentPage'] = $photos[0]['currentPage'];
      $pages['totalPages'] = $photos[0]['totalPages'];
      $pages['requestUri'] = $_SERVER['REQUEST_URI'];
    }

    $body = $this->theme->get('manage.php', array('photos' => $photos, 'pages' => $pages, 'crumb' => getSession()->get('crumb')));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'manage'));
  }
}

