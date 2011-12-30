<?php
/**
  * Action controller for HTML endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ActionController extends BaseController
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

  /**
    * Create a new action by calling the corresponding API endpoint.
    * TODO define the redirect
    *
    * @param string $targetId The ID of the target on which the action will be applied.
    * @param string $targetType The type of object this action is being added to - typically a photo.
    * @return void
    */
  public function create($targetId, $targetType)
  {
    getAuthentication()->requireAuthentication(false);
    getAuthentication()->requireCrumb($_POST['crumb']);
    $res = getApi()->invoke(sprintf('%s.json', Url::actionCreate($targetId, $targetType, false)), EpiRoute::httpPost);
    $result = $res ? '1' : '0';
    // TODO: standardize messaging parameter
    if($targetType == 'photo')
      getRoute()->redirect(sprintf('%s?message=%s', Url::photoView($targetId, null, false), $result));
    else
      getRoute()->run('/error/500');
  }
}
