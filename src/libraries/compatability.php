<?php
// array_column is >= 5.5
if(!function_exists('array_column')) 
{
  function array_column($array, $column)
  {
    return array_map(function($element){ return $element[$column]; }, $array);
  }
}

// parse_ini_string is >= 5.3
if(!function_exists('parse_ini_string'))
{
  function parse_ini_string($str, $ProcessSections=false)
  {
    $lines  = explode("\n", $str);
    $return = Array();
    $inSect = false;
    foreach($lines as $line)
    {
      $line = trim($line);
      if(!$line || $line[0] == "#" || $line[0] == ";")
        continue;
      if($line[0] == "[" && $endIdx = strpos($line, "]"))
      {
        $inSect = substr($line, 1, $endIdx-1);
        continue;
      }
      if(!strpos($line, '=')) // (We don't use "=== false" because value 0 is not valid as well)
        continue;
      
      $tmp = explode("=", $line, 2);
      $tmp[1] = preg_replace(array('/^ ?"/', '/"$/'), '', $tmp[1]);
      if($ProcessSections && $inSect)
        $return[$inSect][trim($tmp[0])] = ltrim($tmp[1]);
      else
        $return[trim($tmp[0])] = ltrim($tmp[1]);
    }
    return $return;
  }
}

// finfo_open is >= 5.3
function get_mime_type($filename)
{
  if(!file_exists($filename))
    return false;

  $type = null;
  if(function_exists("finfo_open"))
  {
    // not supported everywhere https://github.com/openphoto/frontend/issues/368
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $type = finfo_file($finfo, $filename);
  }
  else if(function_exists("mime_content_type"))
  {
    $type = mime_content_type($filename);
  }
  else if(function_exists('exec'))
  {
    $type = exec('/usr/bin/file --mime-type -b ' .escapeshellarg($filename));
    if(empty($type))
      $type = null;
  }

  return $type;
}

// password_verify and password_hash are >= 5.5
// provided in external/password_compat/password.php
