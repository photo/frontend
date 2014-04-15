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
  protected $assets, $assetsRel, $docroot, $cacheDir, $mode, $types;
  public $returnAsHeader = true;

  public function __construct($params = null)
  {
    if(isset($params['config']))
      $config = $params['config'];
    else
      $config = getConfig()->get();

    $mediaVersion = $config->defaults->mediaVersion;
    if(isset($config->site->mediaVersion))
      $mediaVersion = $config->site->mediaVersion;
    $this->cdnPrefix = $config->site->cdnPrefix;
    $this->docroot = $config->paths->docroot;
    $this->cacheDir = sprintf('%s/assets/cache', $this->docroot);
    $this->cacheDirVersioned = sprintf('/assets/versioned/%s/', $mediaVersion); // trailing slash because it's used in a str_replace
    $this->assets = $this->assetsRel = array('js' => array(), 'css' => array());
    $siteMode = $config->site->mode;
    if($siteMode === 'prod')
      $this->mode = self::minified;
    else
      $this->mode = self::combined;

    $this->types = array(
      'css' => 'text/css',
      'gif' => 'image/gif',
      'jpg' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'js' => 'text/javascript',
      'ico' => 'image/vnd.microsoft.icon',
      'png' => 'image/png',
      'tiff' => 'image/tiff',
    );

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

  public function getCombined($type, $version = null)
  {
    $retval = '';
    $files = $this->assets[$type];
    foreach($files as $file)
      $retval .= ($type === self::css ? $this->normalizeUrls($file, $version) : file_get_contents($file)) . "\n";

    return $retval;
  }

  public function getContentType($file)
  {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if(isset($this->types[$ext]))
      return $this->types[$ext];

    return 'text/plain';
  }

  public function getMinified($type)
  {
    $retval = '';
    $files = $this->assets[$type];
    foreach($files as $file)
      $retval .= ($type === self::css ? /*CssMin::minify($this->normalizeUrls($file))*/ $this->normalizeUrls($file) : JSMin::minify(file_get_contents($file))) . "\n";

    return $retval;

  }

  public function getUrl($type, $version = null, $cache = true)
  {
    // generate the hash and see if the file is on disk
    $url = sprintf('/assets/cache/%s/%s/%s%s', $version, $type, $this->mode, implode(',', $this->assetsRel[$type]));
    $hash = sha1($url);
    if($cache && is_dir($this->cacheDir) && file_exists($assetPath = sprintf('%s/%s.%s', $this->cacheDir, $hash, $type)))
      return str_replace($this->docroot, '', $assetPath);

    // else we generate the URL which comebines all the URLs together
    if($cache && is_dir($this->cacheDir) && is_writable($this->cacheDir))
    {
      $contents = $this->mode === self::minified ? $this->getMinified($type) : $this->getCombined($type);
      file_put_contents($assetPath, $contents);
    }
    return $url;
  }

  public function normalizeUrls($file, $contents = null)
  {
    if($contents === null)
      $contents = file_get_contents($file);

    // we only version assets if they are being served from a CDN
    if(!empty($this->cdnPrefix))
      $pathToFile = str_replace(array($this->docroot, '/assets/'), array('', $this->cacheDirVersioned), dirname($file));
    else
      $pathToFile = str_replace($this->docroot, '', dirname($file));

    return str_replace('../', "{$pathToFile}/../", $contents);
  }

  public function returnHeader($file)
  {
    $header = sprintf('Content-Type: %s', $this->getContentType($file));
    if($this->returnAsHeader)
      header($header);
    else
      return $header;
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
}
