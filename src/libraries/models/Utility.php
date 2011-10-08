<?php
class Utility
{
  private static $isMobile;

  public static function callApis($apisToCall)
  {
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

        $response = getApi()->invoke($apiUrlParts['path'], $apiMethod, array("_{$apiMethod}" => $apiParams));
        $params[$name] = $response['result'];

      }
    }
    return $params;
  }

  public static function decrypt($string, $secret = null, $salt = null)
  {
    if($secret === null)
      $secret = getConfig()->get('secrets')->secret;

    if($salt === null)
      $salt = self::getBaseDir();

    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
    $key = md5(sprintf('%s~%s', $salt, $secret));

    $string = base64_decode($string);
    $decryptedString = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_ECB, $iv));
    return $decryptedString;
  }

  public static function encrypt($string, $secret = null, $salt = null)
  {
    if($secret === null)
      $secret = getConfig()->get('secrets')->secret;

    if($salt === null)
      $salt = self::getBaseDir();

    getLogger()->info("encrypt secret = {$secret}");
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
    $key = md5(sprintf('%s~%s', $salt, $secret));

    $encryptedString = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_ECB, $iv);
    return base64_encode($encryptedString);
  }

  public static function getBaseDir()
  {
    return dirname(dirname(dirname(__FILE__)));
  }

  public static function getLicenses($selected = null)
  {
    static $licenses;
    if(!$licenses)
    {
      $licenses = array(
        '' => array('name' => 'All Rights Reserved', 'description' => ''),
        'CC BY' => array('name' => 'Attribution', 'description' => ''),
        'CC BY-SA' => array('name' => 'Attribution-ShareAlike', 'description' => ''),
        'CC BY-ND' => array('name' => 'Attribution-NoDerivs', 'description' => ''),
        'CC BY-NC' => array('name' => 'Attribution-NonCommercial', 'description' => ''),
        'CC BY-NC-SA' => array('name' => 'Attribution-NonCommercial-ShareAlike', 'description' => ''),
        'CC BY-NC-ND' => array('name' => 'Attribution-NonCommercial-NoDerivs', 'description' => '')
      );
    }

    foreach($licenses as $key => $value)
      $licenses[$key]['selected'] = ($key == $selected);

    if($selected === null)
      $licenses['']['selected'] = true;

    return $licenses;
  }

  public static function dateLong($ts, $write = true)
  {
    return self::returnValue(date('l, F jS, Y \a\t g:ia', $ts), $write);
  }

  public static function getPaginationParams($currentPage, $totalPages, $pagesToDisplay)
  {
    $start = 1;
    $end = $pagesToDisplay;
    if($currentPage > ($pagesToDisplay / 2) && $totalPages > $pagesToDisplay)
      $start = floor($currentPage - ($pagesToDisplay / 2));

    if($totalPages <= $pagesToDisplay)
      $end = $totalPages;
    else
      $end = $start+$pagesToDisplay;

    return range($start, $end);
  }

  public static function getProtocol($write = true)
  {
    $protocol = $_SERVER['SERVER_PORT'] != '443' ? 'http' : 'https';
    return self::returnValue($protocol, $write);
  }

  public static function isActiveTab($label)
  {
    if(!isset($_GET['__route__']))
      return $label == 'home';

    $route = $_GET['__route__'];
    switch($label)
    {
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
        if(!empty($route) && preg_match('#^/tags$#', $route))
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

  public static function isMobile()
  {
    if(self::$isMobile !== null)
      return self::$isMobile;

    $detect = new Mobile_Detect();
    self::$isMobile = $detect->isMobile();
    return self::$isMobile;
  }

  public static function getTemplate($template)
  {
    if(!self::isMobile())
      return $template;

    $mobileTemplate = str_replace('.php', '-mobile.php', $template);
    if(!getTheme()->fileExists($mobileTemplate))
      return $template;

    return $mobileTemplate;
  }

  public static function licenseLong($key, $write = true)
  {
    $licenses = self::getLicenses();
    // default it to the key, if the key doesn't exist then assume it's custom
    $license = $key;
    if(isset($licenses[$key]))
    {
      $license = sprintf('%s (%s)', $key, $licenses[$key]['name']);
    }

    return self::returnValue($license, $write);
  }

  public static function permissionAsText($permission, $write = true)
  {
    return self::returnValue(($permission ? 'public' : 'private'), $write);
  }

  public static function plural($int, $word = null, $write = true)
  {
    $word = self::safe($word, false);
    if(empty($word))
      return self::returnValue(($int > 1 ? 's' : ''), $write);
    else
      return self::returnValue(($int > 1 ? "{$word}s" : $word), $write);
  }

  public static function returnValue($value, $write = true)
  {
    if($write)
      echo $value;
    else
      return $value;
  }

  public static function safe($string, $write = true)
  {
    return self::returnValue(htmlspecialchars($string), $write);
  }

  public static function staticMapUrl($latitude, $longitude, $zoom, $size, $type = 'roadmap', $write = true)
  {
    //http://maps.googleapis.com/maps/api/staticmap?center=Brooklyn+Bridge,New+York,NY&zoom=14&size=512x512&maptype=roadmap&markers=color:blue%7Clabel:S%7C40.702147,-74.015794&markers=color:green%7Clabel:G%7C40.711614,-74.012318&markers=color:red%7Ccolor:red%7Clabel:C%7C40.718217,-73.998284&sensor=false
    return self::returnValue("http://maps.googleapis.com/maps/api/staticmap?center={$latitude},{$longitude}&zoom={$zoom}&size={$size}&maptype={$type}&markers=color:gray%7C{$latitude},{$longitude}&sensor=false", $write);
  }

  public static function timeAsText($time, $prefix = null, $suffix = null, $write = true)
  {
    if(empty($time))
      return self::returnValue('', $write);

    $seconds = intval(time() - $time);
    $hours = intval($seconds / 3600);
    if($hours < 0)
      return self::returnValue('--', $write);
    elseif($hours < 1)
      return self::returnValue("{$prefix} a few minutes ago {$suffix}", $write);
    elseif($hours < 24)
      return self::returnValue("{$prefix} {$hours} " . self::plural($hours, 'hour', false) . " ago {$suffix}", $write);

    $days = intval($seconds / 86400);
    if($days <= 7)
      return self::returnValue("{$prefix} {$days} " . self::plural($days, 'day', false) . " ago {$suffix}", $write);

    $weeks = intval($days / 7);
    if($weeks <= 4)
      return self::returnValue("{$prefix} {$weeks} " . self::plural($weeks, 'week', false) . " ago {$suffix}", $write);

    $months = intval($days / 30);
    if($months < 12)
      return self::returnValue("{$prefix} {$months} " . self::plural($months, 'month', false) . " ago {$suffix}", $write);

    $years = intval($days / 365);
    return self::returnValue("{$prefix} {$years} " . self::plural($years, 'year', false) . " ago {$suffix}", $write);
  }

  /**
   * Safe equivalent of getallheaders() the work more often.
   */
  public static function getAllHeaders()
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
