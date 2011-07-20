<?php
class ActionController extends BaseController
{
  public static function actionPost($targetType, $targetId)
  {
    $res = getApi()->invoke("/{$targetType}/{$targetId}/action.json", EpiRoute::httpPost);
    $result = $res ? '1' : '0';
    // TODO: standardize messaging parameter
    getRoute()->redirect("/{$targetType}/{$targetId}?message={$result}");
  }
}
