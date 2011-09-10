<?php
/**
  * Action controller for HTML endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ActionController extends BaseController
{
  /**
    * Create a new action by calling the corresponding API endpoint.
    * TODO define the redirect
    *
    * @param string $targetType The type of object this action is being added to - typically a photo.
    * @param string $targetId The ID of the target on which the action will be applied.
    * @return void
    */
  public static function post($targetType, $targetId)
  {
    getAuthentication()->requireAuthentication(false);
    getAuthentication()->requireCrumb($_POST['crumb']);
    $res = getApi()->invoke("/action/{$targetType}/{$targetId}.json", EpiRoute::httpPost);
    $result = $res ? '1' : '0';
    // TODO: standardize messaging parameter
    getRoute()->redirect("/{$targetType}/{$targetId}?message={$result}");
  }
}
