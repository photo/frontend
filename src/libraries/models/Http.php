<?php
/**
 * Http model.
 *
 * This handles outgoing HTTP interactions.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Http extends BaseModel
{
  /*
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function fireAndForget($url, $method = 'GET', $params = null)
  {
    $url = escapeshellarg($url);
    $method = escapeshellarg($method);
    $paramsAsString = $this->generateParamsAsString($params, $method);
    $command = $this->generateCommand($method, $paramsAsString, $url);
    $this->logger->info($command);
    return $this->executeCommand($command);
  }

  protected function executeCommand($command)
  {
    $this->logger->info($command);
    exec($command, $retval);
    return $retval;
  }

  protected function generateCommand($method, $paramsAsString, $url)
  {
    return sprintf("curl -X %s %s %s > /dev/null 2> /dev/null &", $method, $paramsAsString, $url);
  }

  protected function generateParamsAsString($params, $method)
  {
    $optionLetter = stristr($method, 'get') === false ? 'd' : 'F';
    $paramsAsString = '';
    if(!empty($params) && is_array($params))
    {
      foreach($params as $key => $value)
      {
        if($key[0] === '-')
          $paramsAsString .= sprintf("-%s %s ", preg_replace('/^[^0-9a-zA-Z]{1}$/', '', $key[1]), escapeshellarg($value));
        else
          $paramsAsString .= sprintf("-%s %s ", $optionLetter, escapeshellarg("{$key}={$value}"));
      }
    }
    return $paramsAsString;
  }
}

