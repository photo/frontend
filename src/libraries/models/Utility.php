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

  public function getBaseDir()
  {
    return dirname(dirname(dirname(__FILE__)));
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

  // TODO do not look up $_SERVER
  public function getProtocol($write = true)
  {
    $protocol = $_SERVER['SERVER_PORT'] != '443' ? 'http' : 'https';
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
      case 'photos':
        if(!empty($route) && preg_match('#^/photo#', $route) && !preg_match('#^/photos/upload#', $route))
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
      return $this->returnValue(($int > 1 ? 's' : ''), $write);
    else
      return $this->returnValue(($int > 1 ? "{$word}s" : $word), $write);
  }

  public function returnValue($value, $write = true)
  {
    if($write)
      echo $value;
    else
      return $value;
  }

  public function safe($string, $write = true)
  {
    return $this->returnValue(htmlspecialchars($string), $write);
  }

  public function staticMapUrl($latitude, $longitude, $zoom, $size, $type = 'roadmap', $write = true)
  {
    //http://maps.googleapis.com/maps/api/staticmap?center=Brooklyn+Bridge,New+York,NY&zoom=14&size=512x512&maptype=roadmap&markers=color:blue%7Clabel:S%7C40.702147,-74.015794&markers=color:green%7Clabel:G%7C40.711614,-74.012318&markers=color:red%7Ccolor:red%7Clabel:C%7C40.718217,-73.998284&sensor=false
    return $this->returnValue("http://maps.googleapis.com/maps/api/staticmap?center={$latitude},{$longitude}&zoom={$zoom}&size={$size}&maptype={$type}&markers=color:gray%7C{$latitude},{$longitude}&sensor=false", $write);
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

function getUtility()
{
  static $utility;
  if($utility)
    return $utility;

  $utility = new Utility;
  return $utility;
}
