<?php
/**
 * AssetPipeline model.
 *
 * This handles pipelining static assets.
 * JS and CSS are combined, minified and cached.
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class AssetPipeline
{
  const css = 'css';
  const js = 'js';
  const minified = 'm';
  const combined = 'c';
  protected $assets, $assetsRel, $docroot, $cacheDir, $mode;

  public function __construct($params = null)
  {
    if(isset($params['config']))
      $config = $params['config'];
    else
      $config = getConfig()->get();
    $this->docroot = $config->paths->docroot;
    $this->cacheDir = sprintf('%s/assets/cache', $this->docroot);;
    $this->assets = $this->assetsRel = array('js' => array(), 'css' => array());
    $siteMode = $config->site->mode;
    if($siteMode === 'prod')
      $this->mode = self::minified;
    else
      $this->mode = self::combined;
  }

  public function addCss($src)
  {
    if(file_exists($path = sprintf('%s%s', $this->docroot, $src)))
      $this->addAsset($path, 'css');
    return $this;
  }

  public function addJs($src)
  {
    if(file_exists($path = sprintf('%s%s', $this->docroot, $src)))
      $this->addAsset($path, 'js');

    return $this;
  }

  public function getCombined($type)
  {
    $retval = '';
    $files = $this->assets[$type];
    foreach($files as $file)
      $retval .= ($type === self::css ? $this->normalizeUrls($file) : file_get_contents($file)) . "\n";

    return $retval;
  }

  public function getMinified($type)
  {
    $retval = '';
    $files = $this->assets[$type];
    foreach($files as $file)
      $retval .= ($type === self::css ? /*CssMin::minify($this->normalizeUrls($file))*/ $this->normalizeUrls($file) : JSMin::minify(file_get_contents($file))) . "\n";

    return $retval;

  }

  public function getUrl($type, $version = 'a')
  {
    $url = sprintf('/assets/cache/%s/%s/%s%s', $version, $type, $this->mode, implode(',', $this->assetsRel[$type]));
    $hash = sha1($url);
    if(is_dir($this->cacheDir) && file_exists($assetPath = sprintf('%s/%s.%s', $this->cacheDir, $hash, $type)))
      return str_replace($this->docroot, '', $assetPath);

    
    if(is_dir($this->cacheDir) && is_writable($this->cacheDir))
    {
      $contents = $this->mode === self::minified ? $this->getMinified($type) : $this->getCombined($type);
      file_put_contents($assetPath, $contents);
    }
    
    return $url;
  }

  public function setMode($mode)
  {
    $this->mode = $mode;
    return $this;
  }

  private function addAsset($src, $type)
  {
    // verify this file exists
    if(file_exists($src))
    {
      $this->assets[$type][] = $src;
      $this->assetsRel[$type][] = str_replace($this->docroot, '', $src);
    }
  }

  private function normalizeUrls($file)
  {
    $contents = file_get_contents($file);
    $pathToFile = str_replace($this->docroot, '', dirname($file));
    return str_replace('../', "{$pathToFile}/../", $contents);
  }
}
