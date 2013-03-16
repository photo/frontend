<?php
/**
 * EventTracker model.
 *
 * Tracks user events
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class EventTracker extends BaseModel
{
  private $http;
  public function __construct()
  {
    parent::__construct();
    $this->http = new Http;
  }

  public function track($action, $params = array())
  {
    $user = new User;
    $url = $this->config->customerio->url;
    $id = $this->config->customerio->id;
    $key = $this->config->customerio->key;
    $email = $user->getEmailAddress();
    $emailHash = sha1($email);
    $url = sprintf('%s/%s/events', $url, $emailHash);
    $data = array();
    foreach($params as $k => $v)
      $data[sprintf('data[%s]', $k)] = $v;

    $defaults = array(
      'name' => $action,
      '-u' => sprintf('%s:%s', $id, $key)
    );
    $params = array_merge($defaults, $data);
    $this->http->fireAndForget($url, 'POST', $params);
  }
}
