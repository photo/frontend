<?php
/**
 * Gauges
 * Add tracking JavaScript for http://gaug.es
 *
 * @author Jordan Brock - jordan@brock.id.au
 * Heavily re-used the Google Analytics plugin from Jaisen Mathai (@jmathai)
 */
class GaugesPlugin extends PluginBase
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
    var _gauges = _gauges || [];
    (function() {
      var t   = document.createElement('script');
      t.type  = 'text/javascript';
      t.async = true;
      t.id    = 'gauges-tracker';
      t.setAttribute('data-site-id', '{$conf->id}');
      t.src = '//secure.gaug.es/track.js';
      var s = document.getElementsByTagName('script')[0];
     s.parentNode.insertBefore(t, s);
    })();
  </script>
MKP;
  }
}
