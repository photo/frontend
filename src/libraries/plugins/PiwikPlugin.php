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
    return <<<MKP

  <!-- Piwik -->
  <script type="text/javascript">
    var _paq = _paq || [];
    (function(){
      var u=(("https:" == document.location.protocol) ? "https://{$conf->baseUrl}/" : "http://{$conf->baseUrl}/");
      _paq.push(['setSiteId', {$conf->siteId}]);
      _paq.push(['setTrackerUrl', u+'piwik.php']);
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
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
  OP.Util.on("click:photo-view-modal", function(){
    // Wait for modal action to complete. TODO: This can be more elegant.
    window.setTimeout(function(){
      // Get the existing tracker instance
      var piwikTracker = Piwik.getAsyncTracker();
      // Get URL and title that were changed by modal
      piwikTracker.setCustomUrl(window.location);
      piwikTracker.setDocumentTitle(document.title);
      // Track page view and enable link tracking
      piwikTracker.trackPageView();
      piwikTracker.enableLinkTracking();
    }, 1000);
  }, window);
   </script>
  <!-- End modal Piwik Code -->
MKP;
  }
}
