<?php
/**
 * GoogleAnalytics
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class GoogleAnalyticsPlugin extends PluginBase
{
  private $appId;
  public function __construct()
  {
    parent::__construct();
  }

  public function defineConf()
  {
    return array('id' => null);
  }

  public function renderHead()
  {
    parent::renderHead();
    $conf = $this->getConf();
    return <<<MKP
  <script type="text/javascript">

    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '{$conf->id}']);
    _gaq.push(['_trackPageview']);

    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();

  </script>
MKP;
  }
}
