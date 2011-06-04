<?php
class EpiCurl
{
  const timeout = 3;
  static $inst = null;
  static $singleton = 0;
  private $mc;
  private $msgs;
  private $running;
  private $execStatus;
  private $selectStatus;
  private $sleepIncrement = 1.1;
  private $requests = array();
  private $responses = array();
  private $properties = array();
  private static $timers = array();

  function __construct()
  {
    if(self::$singleton == 0)
    {
      EpiException::raise(new EpiException('This class cannot be instantiated by the new keyword.  You must instantiate it using: $obj = EpiCurl::getInstance();'));
    }

    $this->mc = curl_multi_init();
    $this->properties = array(
      'code'  => CURLINFO_HTTP_CODE,
      'time'  => CURLINFO_TOTAL_TIME,
      'length'=> CURLINFO_CONTENT_LENGTH_DOWNLOAD,
      'type'  => CURLINFO_CONTENT_TYPE,
      'url'   => CURLINFO_EFFECTIVE_URL
      );
  }

  public function addEasyCurl($ch)
  {
    $key = $this->getKey($ch);
    $this->requests[$key] = $ch;
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'headerCallback'));
    $done = array('handle' => $ch);
    $this->storeResponse($done, false);
    $this->startTimer($key);
    return new EpiCurlManager($key);
  }

  public function addCurl($ch)
  {
    $key = $this->getKey($ch);
    $this->requests[$key] = $ch;
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'headerCallback'));

    $code = curl_multi_add_handle($this->mc, $ch);
    $this->startTimer($key);
    
    // (1)
    if($code === CURLM_OK || $code === CURLM_CALL_MULTI_PERFORM)
    {
      do {
          $code = $this->execStatus = curl_multi_exec($this->mc, $this->running);
      } while ($this->execStatus === CURLM_CALL_MULTI_PERFORM);

      return new EpiCurlManager($key);
    }
    else
    {
      return $code;
    }
  }

  public function getResult($key = null)
  {
    if($key != null)
    {
      if(isset($this->responses[$key]))
      {
        return $this->responses[$key];
      }

      $innerSleepInt = $outerSleepInt = 1;
      while($this->running && ($this->execStatus == CURLM_OK || $this->execStatus == CURLM_CALL_MULTI_PERFORM))
      {
        usleep(intval($outerSleepInt));
        $outerSleepInt = intval(max(1, ($outerSleepInt*$this->sleepIncrement)));
        $ms=curl_multi_select($this->mc, 0);
        if($ms > 0)
        {
          do{
            $this->execStatus = curl_multi_exec($this->mc, $this->running);
            usleep(intval($innerSleepInt));
            $innerSleepInt = intval(max(1, ($innerSleepInt*$this->sleepIncrement)));
          }while($this->execStatus==CURLM_CALL_MULTI_PERFORM);
          $innerSleepInt = 1;
        }
        $this->storeResponses();
        if(isset($this->responses[$key]['data']))
        {
          return $this->responses[$key];
        }
        $runningCurrent = $this->running;
      }
      return null;
    }
    return false;
  }

  public static function getSequence()
  {
    return new EpiSequence(self::$timers);
  }

  public static function getTimers()
  {
    return self::$timers;
  }

  private function getKey($ch)
  {
    return (string)$ch;
  }

  private function headerCallback($ch, $header)
  {
    $_header = trim($header);
    $colonPos= strpos($_header, ':');
    if($colonPos > 0)
    {
      $key = substr($_header, 0, $colonPos);
      $val = preg_replace('/^\W+/','',substr($_header, $colonPos));
      $this->responses[$this->getKey($ch)]['headers'][$key] = $val;
    }
    return strlen($header);
  }

  private function storeResponses()
  {
    while($done = curl_multi_info_read($this->mc))
    {
      $this->storeResponse($done);
    }
  }

  private function storeResponse($done, $isAsynchronous = true)
  {
    $key = $this->getKey($done['handle']);
    $this->stopTimer($key, $done);
    if($isAsynchronous)
      $this->responses[$key]['data'] = curl_multi_getcontent($done['handle']);
    else
      $this->responses[$key]['data'] = curl_exec($done['handle']);

    foreach($this->properties as $name => $const)
    {
      $this->responses[$key][$name] = curl_getinfo($done['handle'], $const);
    }
    if($isAsynchronous)
      curl_multi_remove_handle($this->mc, $done['handle']);
    curl_close($done['handle']);
  }

  private function startTimer($key)
  {
    self::$timers[$key]['start'] = microtime(true);
  }

  private function stopTimer($key, $done)
  {
      self::$timers[$key]['end'] = microtime(true);
      self::$timers[$key]['api'] = curl_getinfo($done['handle'], CURLINFO_EFFECTIVE_URL);
      self::$timers[$key]['time'] = curl_getinfo($done['handle'], CURLINFO_TOTAL_TIME);
      self::$timers[$key]['code'] = curl_getinfo($done['handle'], CURLINFO_HTTP_CODE);
  }

  static function getInstance()
  {
    if(self::$inst == null)
    {
      self::$singleton = 1;
      self::$inst = new EpiCurl();
    }

    return self::$inst;
  }
}

class EpiCurlManager
{
  private $key;
  private $epiCurl;

  public function __construct($key)
  {
    $this->key = $key;
    $this->epiCurl = EpiCurl::getInstance();
  }

  public function __get($name)
  {
    $responses = $this->epiCurl->getResult($this->key);
    return isset($responses[$name]) ? $responses[$name] : null;
  }

  public function __isset($name)
  {
    $val = self::__get($name);
    return empty($val);
  }
}

/*
 * Credits:
 *  - (1) Alistair pointed out that curl_multi_add_handle can return CURLM_CALL_MULTI_PERFORM on success.
 */
