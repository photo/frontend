<?php
/**
 * LimitUpload
 *
 * @author Jaisen Mathai - jaisen@jmathai.com
 * Limit uploads of free users
 */
class LimitUploadPlugin extends PluginBase
{
  private $appId;
  public function __construct()
  {
    parent::__construct();
  }

  public function defineConf()
  {
    return array('limit' => 100);
  }

  public function onPhotoUpload()
  {
    $user = new User;
    if($user->getAttribute('isPaid') || isset($_POST['skipLimit']))
      return;

    $conf = $this->getConf();
    $api = getApi();

    $since = strtotime('last monday') - 43200;
    $resp = $api->invoke(sprintf('/photos/since-%s/list.json', date('d F Y', $since)), EpiRoute::httpGet, array('_GET' => array('pageSize' => '1')));
    $result = $resp['result'];
    if(!empty($result) && $result[0]['totalRows'] > $conf->limit)
      throw new Exception('Your account has reached the upload limit', 402);
  }
}
