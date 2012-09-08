<?php
class AssetPipelineOverride extends AssetPipeline
{
  public $docroot;
  public function __construct()
  {
    $config = new stdClass;
    $config->paths = new stdClass;
    $config->paths->docroot = null;
    $config->site = new stdClass;
    $config->site->mode = 'prod';
    $config->site->cdnPrefix = 'a';
    $config->defaults = new stdClass;
    $config->defaults->cdnPrefix = '';
    $config->defaults->mediaVersion = 'a';
    parent::__construct(array('config' => $config));

    if(class_exists('vfsStream'))
    {
      vfsStreamWrapper::register();
      vfsStreamWrapper::setRoot(new vfsStreamDirectory('assetDir'));
      $config->paths->docroot = vfsStream::url('assetDir');
    }

    $this->docroot = $config->paths->docroot;
    $this->cacheDir = sprintf('%s/assets/cache', $this->docroot);;
    $this->assets = $this->assetsRel = array('js' => array(), 'css' => array());
    $siteMode = $config->site->mode;
    if($siteMode === 'prod')
      $this->mode = self::minified;
    else
      $this->mode = self::combined;
    $this->returnAsHeader = false;
  }
}

class AssetPipelineTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    // to test the write methods
    $this->assetPipeline = new AssetPipelineOverride;
  }

  public function testValidateVfsFunctionSuccess()
  {
    if(!class_exists('vfsStream'))
    {
      $this->fail('The vfsStream package was not found. Skipping tests in FileSysemLocalTest. Install using `sudo pear channel-discover pear.bovigo.org && sudo pear install bovigo/vfsStream-beta`');
      return false;
    }
    return true;
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testAddCssWhenNotExists()
  {
    $res = $this->assetPipeline->addCss('/PATH/TO/CSS');
    $this->assertTrue($res instanceof AssetPipelineOverride, 'addCss should return $this');
    $assets = $this->assetPipeline->getUrl(AssetPipeline::css);
    $this->assertTrue(strstr($assets, 'PATH/TO/CSS') === false, 'When file does not exist it should be omitted');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testAddCssWhenExists()
  {
    file_put_contents($this->assetPipeline->docroot.'/CSS', 'foobar');
    $res = $this->assetPipeline->addCss('/CSS');
    $this->assertTrue($res instanceof AssetPipelineOverride, 'addCss should return $this');
    $assets = $this->assetPipeline->getUrl(AssetPipeline::css);
    $this->assertTrue(strstr($assets, 'CSS') !== false, 'When file exists it should NOT be omitted');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testAddJsWhenNotExists()
  {
    $res = $this->assetPipeline->addJs('/PATH/TO/JS');
    $this->assertTrue($res instanceof AssetPipelineOverride, 'addJs should return $this');
    $assets = $this->assetPipeline->getUrl(AssetPipeline::css);
    $this->assertTrue(strstr($assets, 'PATH/TO/JS') === false, 'When file does not exist it should be omitted');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testAddJsWhenExists()
  {
    file_put_contents($this->assetPipeline->docroot.'/JS', 'foobar');
    $res = $this->assetPipeline->addJs('/JS');
    $this->assertTrue($res instanceof AssetPipelineOverride, 'addJs should return $this');
    $assets = $this->assetPipeline->getUrl(AssetPipeline::js);
    $this->assertTrue(strstr($assets, 'JS') !== false, 'When file exists it should NOT be omitted');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testGetCombined()
  {
    file_put_contents($this->assetPipeline->docroot.'/JS', 'foobar');
    $this->assetPipeline->addJs('/JS');
    $res = $this->assetPipeline->getCombined(AssetPipeline::js);
    $this->assertEquals('foobar', trim($res), 'response of combined should be foobar');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testGetMinified()
  {
    file_put_contents($this->assetPipeline->docroot.'/CSS', 'foobar');
    $this->assetPipeline->addCss('/CSS');
    $res = $this->assetPipeline->getCombined(AssetPipeline::css);
    $this->assertEquals('foobar', trim($res), 'response of combined should be foobar');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testGetUrl()
  {
    file_put_contents($this->assetPipeline->docroot.'/JS', 'foobar');
    $this->assetPipeline->addJs('/JS');
    $url = $this->assetPipeline->getUrl(AssetPipeline::js);
    $this->assertTrue(strstr($url, 'JS') !== false, 'When file exists it should NOT be omitted');
  }

  public function testReturnHeader()
  {
    $header = $this->assetPipeline->returnHeader('/path/to/file.js');
    $this->assertEquals('Content-Type: text/javascript', $header);
    $header = $this->assetPipeline->returnHeader('/path/to/file.css');
    $this->assertEquals('Content-Type: text/css', $header);
    $header = $this->assetPipeline->returnHeader('/path/to/file.png');
    $this->assertEquals('Content-Type: image/png', $header);
    $header = $this->assetPipeline->returnHeader('/path/to/file.jpeg');
    $this->assertEquals('Content-Type: image/jpeg', $header);
    $header = $this->assetPipeline->returnHeader('/path/to/file.jpg');
    $this->assertEquals('Content-Type: image/jpeg', $header);
    $header = $this->assetPipeline->returnHeader('/path/to/file.gif');
    $this->assertEquals('Content-Type: image/gif', $header);
    $header = $this->assetPipeline->returnHeader('/path/to/file.docx');
    $this->assertEquals('Content-Type: text/plain', $header);
  }

  public function testReturnHeaderJs()
  {
    $header = $this->assetPipeline->returnHeader('/path/to/javascript.js');
    $this->assertEquals('Content-Type: text/javascript', $header);
  }
}
