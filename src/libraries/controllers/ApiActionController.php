<?php
class ApiActionController extends BaseController
{
  public static function delete($id)
  {
    $status = Action::delete($id);
    if($status)
      return self::success('Action deleted successfully', $id);
    else
      return self::error('Action deletion failure', false);
  }

  public static function post($targetType, $targetId)
  {
    $params = $_POST;
    $params['targetId'] = $targetId;
    $params['targetType'] = $targetType;
    $id = Action::add($params);

    if($id)
      return self::success("Action {$id} created on {$targetType} {$targetId}", array_merge(array('id' => $id), $params));
    else
      return self::failure("Error creating action {$id} on {$targetType} {$targetId}", false);
  }
}
