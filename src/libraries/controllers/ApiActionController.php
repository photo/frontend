<?php
/**
  * Action controller for API endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiActionController extends BaseController
{
  /**
    * Delete a new action by calling the model.
    *
    * @param string $id The ID of the action to be deleted.
    * @return string Standard JSON envelope 
    */
  public static function delete($id)
  {
    getAuthentication()->requireAuthentication();
    $status = Action::delete($id);
    if($status)
      return self::success('Action deleted successfully', $id);
    else
      return self::error('Action deletion failure', false);
  }

  /**
    * Create a new action by calling the model.
    *
    * @param string $targetType The type of object this action is being added to - typically a photo.
    * @param string $targetId The ID of the target on which the action will be applied.
    * @return string Standard JSON envelope 
    */
  public static function post($targetType, $targetId)
  {
    getAuthentication()->requireAuthentication();
    $params = $_POST;
    $params['targetId'] = $targetId;
    $params['targetType'] = $targetType;
    $params['email'] = getSession()->get('email');
    $id = Action::add($params);

    if($id)
      return self::success("Action {$id} created on {$targetType} {$targetId}", array_merge(array('id' => $id), $params));
    else
      return self::failure("Error creating action {$id} on {$targetType} {$targetId}", false);
  }
}
