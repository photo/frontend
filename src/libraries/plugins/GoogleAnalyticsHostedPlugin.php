<?php
/**
 * GoogleAnalyticsHosted
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class GoogleAnalyticsHostedPlugin extends PluginBase
{
  private $configObj;
  public function __construct()
  {
    parent::__construct();
    $this->configObj = getConfig()->get();
  }

  public function defineConf()
  {
    return array('id' => $this->configObj->googleAnalytics->id, 'domainName' => $this->configObj->googleAnalytics->domainName);
  }

  public function renderHead()
  {
    parent::renderHead();
    $conf = $this->getConf();
    $hostname = $_SERVER['HTTP_HOST'];
    $includeScript = '';
    if(!$this->plugin->isActive('GoogleAnalytics'))
    {
      $includeScript = <<<MKP
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
MKP;
    }

    return <<<MKP
  <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['hosted._setAccount', '{$conf->id}']);
    _gaq.push(['hosted._setDomainName', '{$conf->domainName}']);
    _gaq.push(['hosted._trackPageview']);
    _gaq.push(['hosted._setCustomVar', 1, 'hostname', '{$hostname}', 1]);
    {$includeScript}
  </script>
MKP;
  }
}
