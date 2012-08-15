<?php
/**
  * Resource map controller for HTML endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ResourceMapController extends BaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
  }

  public function render($id)
  {
    $resourceResp = $this->api->invoke("/s/{$id}/view.json", EpiRoute::httpGet);
    if($resourceResp['code'] !== 200)
    {
      $this->route->run('/error/404');
      die();
    }

    $this->route->redirect($resourceResp['result']['uri']);
    die();
    // TODO investigate rendering the page at the same URL if the URI is > 255 characters
    /*if(strlen($resourceResp['result']['uri']) <= 255)
    {
      $this->route->redirect($resourceResp['result']['uri']);
      die();
    }

    $this->route->run($resourceResp['result']['uri']);
    die();*/
  }
}
