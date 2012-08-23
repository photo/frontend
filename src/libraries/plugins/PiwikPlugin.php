<?php
/**
 * Piwik Open Source Web Analytics
 *
 * Configuration parameters:
 * baseUrl = [URL to your Piwik host, without protocol]
 * siteId  = [integer, ID of the site, must be set up and ready in Piwik]
 *
 * @author Pixelistik <code@pixelistik.de>
 */
class PiwikPlugin extends PluginBase
{
  private $appId;
  public function __construct()
  {
    parent::__construct();
  }

  public function defineConf()
  {
    return array(
      'baseUrl' => null,
      'siteId' => null
    );
  }

  public function renderHead()
  {
    parent::renderHead();
    $conf = $this->getConf();

    // Include the trackPageView code for all pages EXCEPT photo-detail
    //  photo-detail pages are tracked via the code in renderFooter since
    //  it can be full page loads or AJAX.
    $trackInHead = null;
    if($this->plugin->getData('page') !== 'photo-detail')
    {
      $trackInHead = <<<MKP
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
MKP;
    }
    return <<<MKP
  <!-- Piwik -->
  <script type="text/javascript">
    var _paq = _paq || [];
    (function(){
      var u=(("https:" == document.location.protocol) ? "https://{$conf->baseUrl}/" : "http://{$conf->baseUrl}/");
      _paq.push(['setSiteId', {$conf->siteId}]);
      _paq.push(['setTrackerUrl', u+'piwik.php']);
      {$trackInHead}
      var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript'; g.defer=true; g.async=true; g.src=u+'piwik.js';
      s.parentNode.insertBefore(g,s);
    })();
  </script>
  <!-- End Piwik Code -->
MKP;
  }
  
  public function renderFooter()
  {
    parent::renderFooter();
    return <<<MKP
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
  }
}
