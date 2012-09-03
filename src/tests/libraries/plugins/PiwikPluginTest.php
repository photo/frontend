<?php
$libraryDir = sprintf('%s/libraries', dirname(dirname(dirname(dirname(__FILE__)))));
require_once(sprintf('%s/plugins/PiwikPlugin.php', $libraryDir));

class PiwikPluginTest extends PHPUnit_Framework_TestCase
{
  public function setUp(){
    // Create a configuration object
    $config = new stdClass;
    $config->baseUrl = 'piwik.foo';
    $config->siteId = 123;

    // Inject configuration by mocking getConf()
    $this->piwikPlugin = $this->getMock('PiwikPlugin', array('getConf'));
    $this->piwikPlugin->
      expects($this->any())->
      method('getConf')->
      will($this->returnValue($config));
  }

  /**
   * Test for integration of the Piwik initialisation and normal page
   * tracking code
   */
  public function testRenderHeadNormalPage(){
    $result = $this->piwikPlugin->renderHead();
    $expected = <<<MKP
  <!-- Piwik -->
  <script type="text/javascript">
    var _paq = _paq || [];
    (function(){
      var u=(("https:" == document.location.protocol) ? "https://piwik.foo/" : "http://piwik.foo/");
      _paq.push(['setSiteId', 123]);
      _paq.push(['setTrackerUrl', u+'piwik.php']);
            _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
      var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript'; g.defer=true; g.async=true; g.src=u+'piwik.js';
      s.parentNode.insertBefore(g,s);
    })();
  </script>
  <!-- End Piwik Code -->
MKP;
    $this->assertEquals($expected, $result);
  }
  
  /**
   * Test for integration of the Piwik initialisation code. No actual
   * tracking should be done on photo detail pages, this is triggered by
   * a custom event
   */
  public function testRenderHeadPhotoDetailPage(){
    // Mock to be on a photo-detail page
    $mockPlugin = $this->getMock('Plugin' , array('getData'));
    $mockPlugin->
      expects($this->any())->
      method('getData')->
      will($this->returnValue('photo-detail'));

    $this->piwikPlugin->inject('plugin', $mockPlugin);

    $result = $this->piwikPlugin->renderHead();
    $expected = <<<MKP
  <!-- Piwik -->
  <script type="text/javascript">
    var _paq = _paq || [];
    (function(){
      var u=(("https:" == document.location.protocol) ? "https://piwik.foo/" : "http://piwik.foo/");
      _paq.push(['setSiteId', 123]);
      _paq.push(['setTrackerUrl', u+'piwik.php']);
      
      var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript'; g.defer=true; g.async=true; g.src=u+'piwik.js';
      s.parentNode.insertBefore(g,s);
    })();
  </script>
  <!-- End Piwik Code -->
MKP;
    $this->assertEquals($expected, $result);
  }
  
  /**
   * Test for correct integration of the Piwik code in the footer
   */
  public function testRenderFooter(){
    $result = $this->piwikPlugin->renderFooter();
    $expected = <<<MKP
  <!-- Piwik modal code -->
  <script type="text/javascript">
    OP.Util.on("photo:viewed", function(params){
      // Get URL and title that were changed by modal
      _paq.push(['setCustomUrl', window.location]);
      _paq.push(['setDocumentTitle', document.title]);
      // Track page view and enable link tracking
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
    });
  </script>
  <!-- End modal Piwik Code -->
MKP;
    $this->assertEquals($expected, $result);
  }
}
