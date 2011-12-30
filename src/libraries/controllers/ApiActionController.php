<?php
/**
  * Action controller for API endpoints
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiActionController extends BaseController
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
    * Create a new action by calling the model.
    *
    * @param string $targetId The ID of the target on which the action will be applied.
    * @param string $targetType The type of object this action is being added to - typically a photo.
    * @return string Standard JSON envelope
    */
  public function create($targetId, $targetType)
  {
    getAuthentication()->requireAuthentication(false);
    getAuthentication()->requireCrumb();
    $params = $_POST;
    $params['targetId'] = $targetId;
    $params['targetType'] = $targetType;
    $params['email'] = getSession()->get('email');
    if(isset($_POST['crumb']))
      unset($params['crumb']);
    $id = Action::create($params);

    if($id)
    {
      $action = Action::view($id);
      getPlugin()->invoke('onAction', $action);
      return $this->success("Action {$id} created on {$targetType} {$targetId}", $action);
    }

    return $this->error("Error creating action {$id} on {$targetType} {$targetId}", false);
  }

  /**
    * Delete a new action by calling the model.
    *
    * @param string $id The ID of the action to be deleted.
    * @return string Standard JSON envelope
    */
  public function delete($id)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $status = Action::delete($id);
    if($status)
      return $this->success('Action deleted successfully', true);
    else
      return $this->error('Action deletion failure', false);
  }

  /**
    * Retrieve a single action
    *
    * @param string $id The ID of the action to be retrieved.
    * @return string Standard JSON envelope
    */
  public function view($id)
  {
    getAuthentication()->requireAuthentication(false);
    $action = Action::view($id);
    if($action)
      return $this->success("Action {$id}", $action);

    return $this->error("Could not retrieve action {$id}", false);
  }
}
