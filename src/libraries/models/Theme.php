<?php
/**
 * Theme model.
 *
 * Class to handle all theme rendering and generation.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Theme
{
  const themeDefault = 'fabrizio1.0';
  private $template, $theme, $themeDir, $themeDirWeb, $user;

  public function __construct()
  {
    $this->template = getTemplate();
    $this->theme = getConfig()->get('defaults')->theme;
    $behavior = getConfig()->get('behavior');
    $themeConfig = getConfig()->get('theme');
    $utilityObj = new Utility;
    if($behavior !== null && $utilityObj->isMobile() && $behavior->useDefaultMobile == '1')
    {
      $this->theme = self::themeDefault;
      if(file_exists($mobileSettings = sprintf('%s/%s/config/settings-mobile.ini', getConfig()->get('paths')->themes, $this->getThemeName())))
        getConfig()->loadString(file_get_contents($mobileSettings));
    }
    elseif($themeConfig !== null)
    {
      $this->theme = $themeConfig->name;
    }

    $this->themeDir = sprintf('%s/%s', getConfig()->get('paths')->themes, $this->theme);
    $this->themeDirWeb = str_replace(sprintf('%s/html', dirname(dirname(dirname(__FILE__)))), '', $this->themeDir);
    $this->user = new User;
  }

  public function asset($type, $filename = '', $write = true)
  {
    $utilityObj = new Utility;
    $filename = "/{$filename}";
    switch($type)
    {
      case 'base':
        return $utilityObj->returnValue("{$this->themeDirWeb}{$filename}", $write);
        break;
      case 'image':
        return $utilityObj->returnValue("{$this->themeDirWeb}/images{$filename}", $write);
        break;
      case 'javascript':
        return $utilityObj->returnValue("{$this->themeDirWeb}/javascripts{$filename}", $write);
        break;
      case 'stylesheet':
        return $utilityObj->returnValue("{$this->themeDirWeb}/stylesheets{$filename}", $write);
        break;
      //
      case 'jquery':
        return $utilityObj->returnValue('/assets/javascripts/jquery-1.7.2.min.js', $write);
        break;
      case 'util':
        return $utilityObj->returnValue('/assets/javascripts/openphoto-util.js', $write);
        break;
    }
  }

  public function display($template, $params = null)
  {
    $this->template->display("{$this->themeDir}/templates/{$template}", $params);
  }

  public function fileExists($path)
  {
    return file_exists(sprintf('%s/templates/%s', $this->themeDir, $path));
  }

  public function get($template, $params = null)
  {
    return $this->template->get("{$this->themeDir}/templates/{$template}", $params);
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
    $utilityObj = new Utility;
    if(isset(getConfig()->get($page)->$key))
      return $utilityObj->returnValue(getConfig()->get($page)->$key, $write);
    elseif(isset(getConfig()->get($page)->default))
      return $utilityObj->returnValue(getConfig()->get($page)->default, $write);
    else
      return $utilityObj->returnValue('', $write);
  }

  public function setTheme($theme = null)
  {
    if($theme === null)
      $theme = self::themeDefault;
    $this->theme = $theme;
    $this->themeDir = sprintf('%s/%s', dirname($this->themeDir), $this->theme);
    $this->themeDirWeb = sprintf('%s/%s', dirname($this->themeDirWeb), $this->theme);
  }
}
