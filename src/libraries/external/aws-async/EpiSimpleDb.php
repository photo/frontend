<?php
class EpiSimpleDb extends EpiAWS
{
  // These values are from Amazon's PHP library
	/**
	 * Specify the default queue URL.
	 */
	const DEFAULT_HOST = 'sdb.amazonaws.com';

	/**
	 * Specify the queue URL for the US-East (Northern Virginia) Region.
	 */
	const REGION_US_E1 = self::DEFAULT_HOST;

	/**
	 * Specify the queue URL for the US-West (Northern California) Region.
	 */
	const REGION_US_W1 = 'sdb.us-west-1.amazonaws.com';

	/**
	 * Specify the queue URL for the EU (Ireland) Region.
	 */
	const REGION_EU_W1 = 'sdb.eu-west-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Asia Pacific (Singapore) Region.
	 */
	const REGION_APAC_SE1 = 'sdb.ap-southeast-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Asia Pacific (Japan) Region.
	 */
	const REGION_APAC_NE1 = 'sdb.ap-northeast-1.amazonaws.com';

  private $domain;
  private $method = 'GET';
  protected $host;

  public function __construct($awsKey, $awsSecret, $domain, $host = self::DEFAULT_HOST)
  {
    parent::__construct($awsKey, $awsSecret);
    $this->domain = $domain;
    $this->host = $host;
  }

  public function listDomains()
  {
    $params = array('Action' => 'ListDomains');
    $params = $this->addDefaultParams($params);
    $params = $this->addSignature('GET', '/', $params);
    $url = "http://{$this->host}/?".$this->encodeForQueryString($params);
    return $this->request($url, 'GET');
  }

  public function select($expression, $consistentRead = false, $nextToken = null)
  {
    $params = array(
      'Action' => 'Select', 
      'SelectExpression' => $expression, 
      'ConsistentRead' => $this->booleanAsString($consistentRead),
    );
    if($nextToken !== null)
      $params['NextToken'] = $nextToken;

    $params = $this->addDefaultParams($params);
    $params = $this->addSignature('GET', '/', $params);
    $url = "http://{$this->host}/?".$this->encodeForQueryString($params);
    return $this->request($url, 'GET');
  }

  public function putAttributes($itemName, $attributes)
  {
    $params = array('Action' => 'PutAttributes', 'DomainName' => $this->domain, 'ItemName' => $itemName);

    foreach($attributes as $idx => $val)
    {
      list($name, $value) = each($val);
      $params["Attribute.{$idx}.Name"] = $name;
      $params["Attribute.{$idx}.Value"] = $value;

      //while(current($val))
      //{
      //  list($name, $value) = each($val);
      //}
    }

    $params = $this->addDefaultParams($params);
    $params = $this->addSignature('PUT', '/', $params);

    $url = "http://{$this->host}/?".$this->encodeForQueryString($params);
    return $this->request($url, 'PUT');
  }
}
