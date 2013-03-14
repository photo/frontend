<?php
class ApiNotificationController extends ApiBaseController
{
  private $notification;

  public function __construct()
  {
    parent::__construct();
    $this->notification = new Notification;
  }

  public function create()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    if(empty($_POST['message']) || empty($_POST['type']))
      return $this->error('Not all required parameters were passed in (message, type)', false);

    $mode = Notification::modeConfirm;
    if(isset($_POST['mode']))
      $mode = $_POST['mode'];
    $res = $this->notification->add($_POST['message'], $_POST['type'], $mode);
    if(!$res)
      return $this->error('Could not store notification', false);

    return $this->success('Notification', $res);
  }

  public function delete()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $msg = $this->notification->delete();
    if(empty($msg))
      return $this->error('No notifications to delete.', false);
    return $this->success('Notification deleted', true);
  }

  public function view($type = null)
  {
    getAuthentication()->requireAuthentication();
    $note = $this->notification->get($type);
    if(empty($note))
      return $this->notFound('No notifications found', null);

    return $this->success('Notification', $note);
  }
}
