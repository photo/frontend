<?php
/**
 * CustomerIO
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class CustomerIOPlugin extends PluginBase
{
  private $id, $secret;
  public function __construct()
  {
    parent::__construct();
  }

  public function defineConf()
  {
    return array('id' => null, 'secret' => null);
  }

  public function onPhotoUploaded()
  {
    $eventTracker = new EventTracker;
    $photo = $this->plugin->getData('photo');
    $params = array('id' => $photo['id'], 'permission' => $photo['permission'], 'size' => $photo['size']);
    $eventTracker->track('photo_upload', $params);
  }
}
