<?php
class Utility
{
  private $isMobile, $licenses;

  public function __construct() { }

  public function callApis($apisToCall, $apiObj = null)
  {
    if($apiObj === null)
      $apiObj = getApi();

    $params = array();
    if(!empty($apisToCall))
    {
      foreach($apisToCall as $name => $api)
      {
        $apiParts = explode(' ', $api);
        $apiMethod = strtoupper($apiParts[0]);
        $apiMethod = $apiMethod == 'GET' ? EpiRoute::httpGet : EpiRoute::httpPost;
        $apiUrlParts = parse_url($apiParts[1]);
        $apiParams = array();
        if(isset($apiUrlParts['query']))
          parse_str($apiUrlParts['query'], $apiParams);

        $response = $apiObj->invoke($apiUrlParts['path'], $apiMethod, array("_{$apiMethod}" => $apiParams));
        $params[$name] = $response['result'];

      }
    }
    return $params;
  }

  // http://en.wikipedia.org/wiki/Decimal_degrees
  public function decreaseGeolocationPrecision($value)
  {
    return round($value);
  }

  public function decrypt($string, $secret = null, $salt = null)
  {
    if($secret === null)
      $secret = getConfig()->get('secrets')->secret;

    if($salt === null)
      $salt = $this->getBaseDir();

    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
    $key = md5(sprintf('%s~%s', $salt, $secret));

    $string = base64_decode($string);
    $decryptedString = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_ECB, $iv));
    return $decryptedString;
  }

  public function diagnosticLine($status, $message)
  {
    $status = (bool)$status;
    $label = $status ? 'success' : 'failure';
    return array('status' => $status, 'label' => $label, 'message' => $message);
  }

  public function enableBetaFeatures()
  {
    $config = getConfig()->get();
    return $config->site->enableBetaFeatures === '1';
  }

  public function encrypt($string, $secret = null, $salt = null)
  {
    if($secret === null)
      $secret = getConfig()->get('secrets')->secret;

    if($salt === null)
      $salt = $this->getBaseDir();

    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
    $key = md5(sprintf('%s~%s', $salt, $secret));

    $encryptedString = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_ECB, $iv);
    return base64_encode($encryptedString);
  }

  public function getAbsoluteUrl($path = '/', $write = true)
  {
    return $this->returnValue(sprintf('%s://%s%s', $this->getProtocol(false), $this->getHost(false), $path), $write);
  }

  public function getBaseDir()
  {
    return dirname(dirname(dirname(__FILE__)));
  }

  public function getConfigFile($new = false)
  {
    $configFile = sprintf('%s/userdata/configs/%s.ini', $this->getBaseDir(), $this->getHost($new));
    if(!getConfig()->exists($configFile))
      return false;
    return $configFile;
  }

  public function getHost($new = false)
  {
    if($new === false)
      return $_SERVER['HTTP_HOST'];

    $config = getConfig()->get();
    return str_replace($config->site->baseHost, $config->site->rewriteHost, $_SERVER['HTTP_HOST']);
  }

  public function getLicenses($selected = null)
  {
    if(!$this->licenses)
    {
      $this->licenses = array(
        '' => array('name' => 'All Rights Reserved', 'description' => ''),
        'CC BY' => array('name' => 'Attribution', 'description' => '', 'link' => 'http://creativecommons.org/licenses/by/3.0'),
        'CC BY-SA' => array('name' => 'Attribution-ShareAlike', 'description' => '', 'link' => 'http://creativecommons.org/licenses/by-sa/3.0'),
        'CC BY-ND' => array('name' => 'Attribution-NoDerivs', 'description' => '', 'link' => 'http://creativecommons.org/licenses/by-nd/3.0'),
        'CC BY-NC' => array('name' => 'Attribution-NonCommercial', 'description' => '', 'link' => 'http://creativecommons.org/licenses/by-nc/3.0' ),
        'CC BY-NC-SA' => array('name' => 'Attribution-NonCommercial-ShareAlike', 'description' => '', 'link' => 'http://creativecommons.org/licenses/by-nc-sa/3.0'),
        'CC BY-NC-ND' => array('name' => 'Attribution-NonCommercial-NoDerivs', 'description' => '', 'link' => 'http://creativecommons.org/licenses/by-nc-nd/3.0')
      );
    }

    foreach($this->licenses as $key => $value)
      $this->licenses[$key]['selected'] = ($key == $selected);

    if($selected === null)
      $this->licenses['']['selected'] = true;

    return $this->licenses;
  }

  public function getPath()
  {
    return $_SERVER['REQUEST_URI'];
  }

  public function dateLong($ts, $write = true)
  {
    if(empty($ts))
      return 'Unknown';
    return $this->returnValue(date('l, F jS, Y \a\t g:ia', $ts), $write);
  }

  // http://stackoverflow.com/a/1268642
  public function generateIniString($array, $hasSections = false)
  {
    $retval = ''; 
    if($hasSections)
    { 
      foreach ($array as $key=>$elem)
      { 
        $retval .= "\n[{$key}]\n"; 
        if(is_array($elem))
        {
          foreach ($elem as $key2=>$elem2)
          { 
            if(is_array($elem2)) 
            { 
              for($i=0;$i<count($elem2);$i++) 
              { 
                $retval .= $key2."[] = \"".$elem2[$i]."\"\n"; 
              } 
            } 
            else if($elem2=="") $retval .= $key2." = \n"; 
            else $retval .= $key2." = \"".$elem2."\"\n"; 
          } 
        }
      } 
    } 
    else
    { 
      foreach ($array as $key=>$elem)
      { 
        if(is_array($elem)) 
        { 
          for($i=0;$i<count($elem);$i++) 
          { 
            $retval .= $key."[] = \"".$elem[$i]."\"\n"; 
          } 
        } 
        else if($elem=="") $retval .= $key." = \n";
        else $retval .= $key." = \"".$elem."\"\n"; 
      } 
    } 

    return trim($retval);
  }

  public function getEmailHandle($email, $write = true)
  {
    return $this->returnValue(substr($email, 0, strpos($email, '@')), $write);
  }

  public function getPaginationParams($currentPage, $totalPages, $pagesToDisplay)
  {
    $start = 1;
    $end = $pagesToDisplay;
    if($currentPage > ($pagesToDisplay / 2) && $totalPages > $pagesToDisplay)
      $start = floor($currentPage - ($pagesToDisplay / 2));

    if($totalPages <= $pagesToDisplay)
      $end = $totalPages;
    else
      $end = min($totalPages, ($start+$pagesToDisplay));

    return range($start, $end);
  }

  public function getProtocol($write = true)
  {
    $protocol = 'http';

    // If any of these match then we should use https
    if(isset($_SERVER['HTTPS']) && strncasecmp('on', $_SERVER['HTTPS'], 2) === 0)
      $protocol = 'https';

    //  It's possible that HTTPS is NULL in the case of SSL being terminated higher up the chain
    if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strncasecmp('https', $_SERVER['HTTP_X_FORWARDED_PROTO'], 5) === 0)
      $protocol = 'https';

    return $this->returnValue($protocol, $write);
  }

  public function isActiveTab($label)
  {
    if(!isset($_GET['__route__']))
      return $label == 'home';

    $route = $_GET['__route__'];
    switch($label)
    {
      case 'album':
      case 'albums':
        if(!empty($route) && preg_match('#^/album#', $route))
          return true;
        break;
      case 'home':
        if(preg_match('#^/$#', $route))
          return true;
        return false;
        break;
      case 'photo':
        if(!empty($route) && (preg_match('#^/photo/#', $route) || preg_match('#^/p/.+#', $route)))
          return true;
        return false;
        break;
      case 'photos':
        if(!empty($route) && (preg_match('#^/photos/#', $route) && !preg_match('#^/photos/upload#', $route)))
          return true;
        return false;
        break;
      case 'photos-album':
        if(!empty($route) && (preg_match('#^/photos/#', $route) && preg_match('#album-[0-9a-z]+#', $route)))
          return true;
        return false;
        break;
      case 'tags':
        if(!empty($route) && preg_match('#^/tags/list#', $route))
          return true;
        return false;
        break;
      case 'upload':
        if(!empty($route) && preg_match('#^/photos/upload#', $route))
          return true;
        return false;
        break;
      case 'manage':
        if(!empty($route) && preg_match('#^/manage#', $route))
          return true;
        return false;
        break;
    }
  }

  public function isMobile()
  {
    if($this->isMobile !== null)
      return $this->isMobile;

    $detect = new Mobile_Detect();
    $this->isMobile = $detect->isMobile();
    return $this->isMobile;
  }


  public function isValidMimeType($filename)
  {
    $type = get_mime_type($filename);
    if(preg_match('/jpg|jpeg|gif|png|tif|tiff$/', $type))
      return true;
    return false;
  }

  public function getTemplate($template)
  {
    if(!$this->isMobile())
      return $template;

    $mobileTemplate = str_replace('.php', '-mobile.php', $template);
    if(!file_exists($mobileTemplate) && !getTheme()->fileExists($mobileTemplate))
      return $template;

    return $mobileTemplate;
  }

  public function licenseLong($key, $write = true)
  {
    $licenses = $this->getLicenses();
    // default it to the key, if the key doesn't exist then assume it's custom
    $license = $key;
    if(isset($licenses[$key]))
      $license = sprintf('%s (%s)', $key, $licenses[$key]['name']);

    return $this->returnValue($license, $write);
  }

  public function licenseName($key, $write = true)
  {
    $licenses = $this->getLicenses();
    // default it to the key, if the key doesn't exist then assume it's custom
    $license = $key;
    if(isset($licenses[$key]))
      $license = $licenses[$key]['name'];

    return $this->returnValue($license, $write);
  }

  public function licenseLink($key, $write = true)
  {
    $licenses = $this->getLicenses();
    $link = '';
    if(isset($licenses[$key]) && isset($licenses[$key]['link']))
      $link = $licenses[$key]['link'];
    return $this->returnValue($link, $write);
  }

  public function permissionAsText($permission, $write = true)
  {
    return $this->returnValue(($permission ? 'public' : 'private'), $write);
  }

  public function plural($int, $word = null, $write = true)
  {
    $word = $this->safe($word, false);
    if(empty($word))
      return $this->returnValue(($int != 1 ? 's' : ''), $write);
    else
      return $this->returnValue(($int != 1 ? "{$word}s" : $word), $write);
  }

  public function selectPlural($int, $singularForm, $pluralForm, $write = true)
  {
    $singularForm = $this->safe($singularForm, false);
    $pluralForm = $this->safe($pluralForm, false);
    return $this->returnValue(($int != 1 ? $pluralForm : $singularForm), $write);
  }

  public function posessive($noun, $write = true)
  {
    if(substr($noun, -1) === 's')
      $val = sprintf('%s', $noun);
    else
      $val = sprintf("%s's", $noun);

    return $this->returnValue($val, $write);
  }

  public function returnValue($value, $write = true)
  {
    if($write)
      echo $value;
    else
      return $value;
  }

  public function safe($string/*[, $allowedTags], $write = true*/)
  {
    $argCnt = func_num_args();
    if($argCnt === 1)
      return $this->returnValue(htmlspecialchars($string), true);

    $args = func_get_args();
    if(gettype($args[1]) == 'string')
    {
      $write = $argCnt == 3 ? $args[2] : true;
      return $this->returnValue(strip_tags($string, $args[1]), $write);
    }
    else
    {
      $write = $argCnt == 2 ? $args[1] : true;
      return $this->returnValue(htmlspecialchars($string), $write);
    }
  }

  public function mapLinkUrl($latitude, $longitude, $zoom, $write = true)
  {
    return $this->returnValue(getMap()->linkUrl($latitude, $longitude, $zoom), $write);
  }

  public function staticMapUrl($latitude, $longitude, $zoom, $size, $type = 'roadmap', $write = true)
  {
    return $this->returnValue(getMap()->staticMap($latitude, $longitude, $zoom, $size, $type), $write);
  }

  public function timeAsText($time, $prefix = null, $suffix = null, $write = true)
  {
    if(empty($time))
      return $this->returnValue('', $write);

    $seconds = intval(time() - $time);
    $hours = intval($seconds / 3600);
    if($hours < 0)
      return $this->returnValue('--', $write);
    elseif($hours < 1)
      return $this->returnValue("{$prefix} a few minutes ago {$suffix}", $write);
    elseif($hours < 24)
      return $this->returnValue("{$prefix} {$hours} " . $this->plural($hours, 'hour', false) . " ago {$suffix}", $write);

    $days = intval(round($seconds / 86400));
    if($days <= 7)
      return $this->returnValue("{$prefix} {$days} " . $this->plural($days, 'day', false) . " ago {$suffix}", $write);

    $weeks = intval(round($days / 7));
    if($weeks <= 4)
      return $this->returnValue("{$prefix} {$weeks} " . $this->plural($weeks, 'week', false) . " ago {$suffix}", $write);

    $months = intval(round($days / 30));
    if($months < 12)
      return $this->returnValue("{$prefix} {$months} " . $this->plural($months, 'month', false) . " ago {$suffix}", $write);

    $years = intval(round($days / 365));
    return $this->returnValue("{$prefix} {$years} " . $this->plural($years, 'year', false) . " ago {$suffix}", $write);
  }

  /**
   * Safe equivalent of getallheaders() the work more often.
   */
  public function getAllHeaders()
  {
    // fetch values from header
    // See issue 171: getallheaders() might not be available on FastCGI or non-Apache.
    if(function_exists('getallheaders'))
    {
      $headers = getallheaders();
    }
    else
    {
      $headers = array();
      // solution suggested by http://us.php.net/manual/en/function.apache-request-headers.php#70810
      foreach ($_SERVER as $name => $value)
      {
        if (substr($name, 0, 5) == 'HTTP_')
        {
          $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
      }
    }
    return $headers;
  }
}
