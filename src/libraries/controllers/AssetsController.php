<?php
/**
  * Tag controller for API endpoints
  *
  * This controller does much of the dispatching to the Tag controller for all tag requests.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class AssetsController extends BaseController
{
  private $types, $pipeline;

  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
    $this->pipeline = getAssetPipeline();
  }

  public function get($version, $type, $compression, $files)
  {
    $files = (array)explode(',', $files);
    foreach($files as $file)
    {
      if($type === 'css')
        $this->pipeline->addCss($file);
      elseif($type === 'js')
        $this->pipeline->addJs($file);
    }

    $this->pipeline->returnHeader($files[0]);
    if($compression === 'm')
      echo $this->pipeline->getMinified($type, $version);
    elseif($compression === 'c')
      echo $this->pipeline->getCombined($type, $version);
  }

  public function lessc()
  {
    if(!isset($_GET['f']))
    {
      $this->route->run('/error/404');
      return;
    }

    $f = $_GET['f'];

    $key = $this->config->site->mediaVersion;
    header('Content-type: text/css');
    header(sprintf('Last-Modified: %s GMT', gmdate('D, d M Y H:i:s', strtotime('-1 year')))); 
    header(sprintf('Etag: %s', md5(sprintf('%s~%s', $key, $f))));

    if(!is_array($f))
      $f = (array)explode(',', $f);
    $theme = getTheme();
    $less = new lessc;
    foreach($f as $file)
    {
      $fullPath = realpath(sprintf('%s/%s/stylesheets/%s', $this->config->paths->themes, $theme->getThemeName(), $file));
      if(file_exists($fullPath) && preg_match('/\.less$/', $file) === 1 && strpos($fullPath, $this->config->paths->themes) === 0)
      {
        if($this->config->site->mode === 'prod')
        {
          $cacheFile = realpath(sprintf('%s/assets/cache', $this->config->paths->docroot)) . '/' . md5($theme->getThemeName() . $file) . '-' . basename($file) . '.css.cache';
          $cssOutput = $this->getCompiledLessFile($fullPath, $cacheFile, $less);
        }
        else
        {
          $cssOutput = $this->pipeline->normalizeUrls(dirname($fullPath), $less->compileFile($fullPath));
        }
        echo $cssOutput;
      }
    }
  }

  private function getCompiledLessFile($inputFile, $cacheFile, $lessc)
  {
    if (file_exists($cacheFile)) 
      $cache = unserialize(file_get_contents($cacheFile));
    else 
      $cache = $inputFile;

    $newCache = $lessc->cachedCompile($cache);

    if (!is_array($cache) || $newCache['updated'] > $cache['updated']) 
    {
      $normalizedCss = $this->pipeline->normalizeUrls(dirname($inputFile), $newCache['compiled']);
      $newCache['compiled'] = $normalizedCss;
      file_put_contents($cacheFile, serialize($newCache));
    }
    return $newCache['compiled'];
  }

  public function staticAsset($file)
  {
    $this->pipeline->returnHeader($file);
    readfile(sprintf('%s/assets/%s', $this->config->paths->docroot, $file));
    die();
  }


}
