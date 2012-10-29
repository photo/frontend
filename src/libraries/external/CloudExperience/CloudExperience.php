<?php
class CloudExperience
{
  private $accessToken, $baseUrl, $clientId, $clientSecret, $version;

  public function __construct($clientId, $clientSecret, $accessToken = null)
  {
    $this->version = '1';
    $this->baseUrl = 'https://api.cx.com';
    $this->uploadUrl = 'https://data.cx.com';

    $this->clientId = $clientId;
    $this->clientSecret = $clientSecret;
    if($accessToken !== null)
      $this->setAccessToken($accessToken);
  }
  public function get($endpoint, $params = array())
  {
    return $this->call('GET', $this->getUrl($endpoint), $params);
  }

  public function getAuthorizationUrl($callback)
  {
    if(preg_match('/^https/', $callback) === 0)
      throw new CloudExperience_InvalidRedirectUri_Exception('Your callback must be https');

    return sprintf('https://www.cx.com/mycx/oauth/authorize?client_id=%s&redirect_uri=%s', $this->clientId, urlencode($callback));
  }

  public function getAccessToken($code, $callback)
  {
    $url = sprintf('%s/%s/oauth/token', $this->baseUrl, $this->version);
    $params = array(
      'grant_type' => 'authorization_code',
      'code' => $code,
      'redirect_uri' => $callback
    );
    $headers = array(
      sprintf('Authorization: Basic %s', base64_encode(sprintf('%s:%s', $this->clientId, $this->clientSecret)))
    );
    return $this->call('POST', $url, $params, $headers);
  }

  public function getUrl($endpoint, $isUpload = false)
  {
    if($isUpload)
      return sprintf('%s/%s%s?access_token=%s&client_id=%s', $this->uploadUrl, $this->version, $endpoint, $this->accessToken, $this->clientId);
    else
      return sprintf('%s/%s%s?access_token=%s&client_id=%s', $this->baseUrl, $this->version, $endpoint, $this->accessToken, $this->clientId);
  }

  public function post($endpoint, $params = array())
  {
    return $this->call('POST', $this->getUrl($endpoint), $params);
  }

  public function setAccessToken($accessToken)
  {
    if(empty($accessToken))
      return false;

    $this->accessToken = $accessToken;
    return true;
  }

  public function upload($endpoint, $params = array())
  {
    return $this->call('POST', $this->getUrl($endpoint, true), $params);
  }

  private function call($method, $url, $params = array(), $headers = array())
  {
    if($method === 'GET' && !empty($params))
      $url = sprintf('%s&%s', $url, http_build_query($params));

    $ch = curl_init($url);
    
    if($method === 'POST' && !empty($params))
      curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

    if(!empty($headers))
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($ch);
    curl_close($ch);
    $resp = json_decode($resp, 1);
    return $resp;
  }
}

class CloudExperience_Exception extends Exception {}
class CloudExperience_InvalidRedirectUri_Exception extends CloudExperience_Exception {}
