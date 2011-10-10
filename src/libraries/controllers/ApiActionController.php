<?php
/**
  * Action controller for API endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiActionController extends BaseController
{
  /**
    * Create a new action by calling the model.
    *
    * @param string $targetId The ID of the target on which the action will be applied.
    * @param string $targetType The type of object this action is being added to - typically a photo.
    * @return string Standard JSON envelope
    */
  public static function create($targetId, $targetType)
  {
    getAuthentication()->requireAuthentication(false);
    getAuthentication()->requireCrumb();
    $params = $_POST;
    $params['targetId'] = $targetId;
    $params['targetType'] = $targetType;
    $params['email'] = getSession()->get('email');
    if(isset($_POST['crumb']))
    {
      unset($params['crumb']);
    }
    $id = Action::add($params);

    if($id)
      return self::success("Action {$id} created on {$targetType} {$targetId}", array_merge(array('id' => $id), $params));
    else
      return self::error("Error creating action {$id} on {$targetType} {$targetId}", false);
  }

  /**
    * Delete a new action by calling the model.
    *
    * @param string $id The ID of the action to be deleted.
    * @return string Standard JSON envelope
    */
  public static function delete($id)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $status = Action::delete($id);
    if($status)
      return self::success('Action deleted successfully', true);
    else
      return self::error('Action deletion failure', false);
  }
}
