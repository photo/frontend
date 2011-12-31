<?php
/**
 * Theme model.
 *
 * Class to handle all theme rendering and generation.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Theme
{
  const themeDefault = 'default';
  private $theme, $themeDir, $themeDirWeb;

  public function __construct()
  {
    $this->theme = getConfig()->get('defaults')->theme;
    $behavior = getConfig()->get('behavior');
    $themeConfig = getConfig()->get('theme');
    if($behavior !== null && Utility::isMobile() && $behavior->useDefaultMobile == '1')
    {
      $this->theme = self::themeDefault;
      if(file_exists($mobileSettings = sprintf('%s/%s/config/settings-mobile.ini', getConfig()->get('paths')->themes, $this->getThemeName())))
        getConfig()->load($mobileSettings);
    }
    elseif($themeConfig !== null)
    {
      $this->theme = $themeConfig->name;
    }

    $this->themeDir = sprintf('%s/%s', getConfig()->get('paths')->themes, $this->theme);
    $this->themeDirWeb = str_replace(sprintf('%s/html', dirname(dirname(dirname(__FILE__)))), '', $this->themeDir);
  }

  public function asset($type, $filename = '', $write = true)
  {
    $filename = "/{$filename}";
    switch($type)
    {
      case 'base':
        return Utility::returnValue("{$this->themeDirWeb}{$filename}", $write);
        break;
      case 'image':
        return Utility::returnValue("{$this->themeDirWeb}/images{$filename}", $write);
        break;
      case 'javascript':
        return Utility::returnValue("{$this->themeDirWeb}/javascripts{$filename}", $write);
        break;
      case 'stylesheet':
        return Utility::returnValue("{$this->themeDirWeb}/stylesheets{$filename}", $write);
        break;
      //
      case 'jquery':
        return Utility::returnValue('/assets/javascripts/jquery-1.6.2.min.js', $write);
        break;
      case 'util':
        return Utility::returnValue('/assets/javascripts/openphoto-util.js', $write);
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

  public function getThemeName()
  {
    return $this->theme;
  }

  public function getThemes()
  {
    $dir = dir(getConfig()->get('paths')->themes);
    $dirs = array();
    while (($name = $dir->read()) !== false)
    {
      if(substr($name, 0, 1) == '.')
        continue;

      $dirs[] = $name;
    }
    return $dirs;
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

  public function setTheme($theme)
  {
    $this->theme = 'beisel';
    $this->themeDir = sprintf('%s/%s', dirname($this->themeDir), $this->theme);
    $this->themeDirWeb = sprintf('%s/%s', dirname($this->themeDirWeb), $this->theme);
  }
}

/**
  * The public interface for instantiating a theme obect.
  *
  * @return object A theme object
  */
function getTheme($singleton = true)
{
  static $theme;
  if($singleton && !$theme)
  {
    $theme = new Theme();
    return $theme;
  }

  return new Theme();
}
