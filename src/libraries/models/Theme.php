<?php
/**
 * Theme model.
 *
 * Class to handle all theme rendering and generation.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Theme
{
  private $theme, $themeDir, $themeDirWeb;

  public function __construct()
  {
    $this->theme = getConfig()->get('site')->theme;
    $this->themeDir = sprintf('%s/%s', getConfig()->get('paths')->themes, $this->theme);
    $this->themeDirWeb = str_replace(sprintf('%s/html', dirname(dirname(dirname(__FILE__)))), '', $this->themeDir);
  }

  public function asset($type, $filename = '')
  {
    $filename = "/{$filename}";
    switch($type)
    {
      case 'base':
        echo "{$this->themeDirWeb}{$filename}";
        break;
      case 'image':
        echo "{$this->themeDirWeb}/images{$filename}";
        break;
      case 'javascript':
        echo "{$this->themeDirWeb}/javascripts{$filename}";
        break;
      case 'stylesheet':
        echo "{$this->themeDirWeb}/stylesheets{$filename}";
        break;
      //
      case 'jquery':
        echo '/assets/javascripts/jquery-1.6.2.min.js';
        break;
      case 'util':
        echo '/assets/javascripts/openphoto-util.js';
        break;
    }
  }

  public function display($template, $params = null)
  {
    getTemplate()->display("{$this->themeDir}/templates/{$template}", $params);
  }

  public function fileExists($path)
  {
    return file_exists(sprintf('%s/templates/%s', $this->themeDir, $path));
  }

  public function get($template, $params = null)
  {
    return getTemplate()->get("{$this->themeDir}/templates/{$template}", $params);
  }

  public function meta($page, $key, $write = true)
  {
    if(isset(getConfig()->get($page)->$key))
      return Utility::returnValue(getConfig()->get($page)->$key, $write);
    elseif(isset(getConfig()->get($page)->default))
      return Utility::returnValue(getConfig()->get($page)->default, $write);
    else
      return Utility::returnValue('', $write);
  }
}

/**
  * The public interface for instantiating a theme obect.
  *
  * @return object A theme object
  */
function getTheme()
{
  static $theme;
  if(!$theme)
    $theme = new Theme();
  return $theme;
}
