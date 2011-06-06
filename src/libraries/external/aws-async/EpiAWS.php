<?php
class EpiAWS
{
  const DATE_FORMAT_ISO8601 = 'Y-m-d\TH:i:s\Z';
  const HASH_ALGO = 'sha256';

  protected $awsKey;
  protected $awsSecret;
  protected $signatureMethod = 'HmacSHA256';
  protected $signatureVersion = '2';
  protected $version = '2009-04-15';
  protected $isAsynchronous = false;

  public function __construct($awsKey, $awsSecret)
  {
    $this->awsKey = $awsKey;
    $this->awsSecret = $awsSecret;
  }

  public function useAsynchronous($bool)
  {
    $this->isAsynchronous = $bool;
  }

  protected function addDefaultParams($params)
  {
    $out = array_merge(
      array(
        'AWSAccessKeyId' => $this->awsKey,
        'SignatureVersion' => $this->signatureVersion,
        'SignatureMethod' => $this->signatureMethod,
        'Timestamp' => $this->timestamp(),
        'Version' => $this->version,
      ),
      $params
    );
    uksort($params, 'strcmp');
    return $out;
  }

  protected function addSignature($method, $path, $params)
  {
    $params['Signature'] = $this->generateSignature($method, $path, $params);
    return $params;
  }

  protected function booleanAsString($boolean)
  {
    return $boolean ? 'true' : 'false';
  }

  protected function encodeForQueryString($params)
  {
    $out = '';
    foreach($params as $key => $val)
      $out .= $this->encode($key) . '=' . $this->encode($val) . '&';

    return substr($out, 0, -1);
  }

  protected function encode($string)
  {
    return str_replace('%7E', '~', rawurlencode($string));
  }

  protected function generateSignature($method, $path, $params)
  {
    uksort($params, 'strcmp');
    $signableParams = $this->paramsAsSignableString($params);
    $stringToSign = "{$method}\n{$this->host}\n{$path}\n{$signableParams}";
    return base64_encode(hash_hmac(self::HASH_ALGO, $stringToSign, $this->awsSecret, true));
  }

  protected function paramsAsSignableString($params)
  {
    $out = '';
    foreach($params as $key => $val)
      $out .= $this->encode($key) . '=' . $this->encode($val) . '&';

    return substr($out, 0, -1);
  }

  protected function request($url, $verb='GET', $params = null)
  {
    $args = func_get_args();
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $resp = new EpiAWSResponse(EpiCurl::getInstance()->addCurl($ch));
    if(!$this->isAsynchronous)
      $resp->response;

    return $resp;
  }

  protected function timestamp()
  {
    return gmdate(self::DATE_FORMAT_ISO8601, strtotime('+15 minutes'));
  }
}

class EpiAWSResponse
{
  private $__resp;
  public function __construct($response)
  {
    $this->__resp = $response;
  }

  // ensure that calls complete by blocking for results, NOOP if already returned
  public function __destruct()
  {
    $this->responseText;
  }
  
  public function __get($name)
  {
    $accessible = array('responseText'=>1,'headers'=>1,'code'=>1);
    $this->responseText = $this->__resp->data;
    $this->headers      = $this->__resp->headers;
    $this->code         = $this->__resp->code;
    if(isset($this->$name) && isset($accessible[$name]) && $accessible[$name])
      return $this->$name;

    // Call appears ok so we can fill in the response
    $this->response     = simplexml_load_string($this->responseText);
    foreach($this->response as $key => $val)
      $this->$key = $val;
  }

  public function __isset($name)
  {
    $value = self::__get($name);
    return !empty($name);
  }
}
