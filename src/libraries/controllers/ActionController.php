<?php
class ActionController extends BaseController
{
  public static function post($targetType, $targetId)
  {
    $res = getApi()->invoke("/action/{$targetType}/{$targetId}.json", EpiRoute::httpPost);
    $result = $res ? '1' : '0';
    // TODO: standardize messaging parameter
    getRoute()->redirect("/{$targetType}/{$targetId}?message={$result}");
  }
}
