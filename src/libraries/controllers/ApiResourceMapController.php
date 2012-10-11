<?php
/**
  * ResourceMap controller for API endpoints
  *
  * This controller does much of the dispatching to the Photo controller for all photo requests.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiResourceMapController extends ApiBaseController
{
  private $resourceMap;

  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
    $this->resourceMap = new ResourceMap;
  }

  public function create()
  {
    getAuthentication()->requireAuthentication();
    $id = $this->resourceMap->create($_POST);
    if(!$id)
      return $this->error('Could not generate resource map.', false);

    $resourceResp = $this->api->invoke("/s/{$id}/view.json");
    if($resourceResp['code'] !== 200)
      return $this->error('Could not retrieve resource map after creating it', false);
    return $this->created("Resource map {$id} successfully created", $resourceResp['result']);
  }

  public function view($id)
  {
    $resourceMap = $this->resourceMap->getResourceMap($id);
    if(!$resourceMap)
      return $this->error('Could not get resource map', false);
    return $this->success("Successfully retrieved resource map {$id}", $resourceMap);
  }
}

