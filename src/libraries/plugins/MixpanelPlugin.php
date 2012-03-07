<?php
/**
 * Mixpanel
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class MixpanelPlugin extends PluginBase
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
<!-- start Mixpanel --><script type="text/javascript">var mpq=[];mpq.push(["init","{$conf->id}"]);(function(){var b,a,e,d,c;b=document.createElement("script");b.type="text/javascript";b.async=true;b.src=(document.location.protocol==="https:"?"https:":"http:")+"//api.mixpanel.com/site_media/js/api/mixpanel.js";a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(b,a);e=function(f){return function(){mpq.push([f].concat(Array.prototype.slice.call(arguments,0)))}};d=["init","track","track_links","track_forms","register","register_once","identify","name_tag","set_config"];for(c=0;c<d.length;c++){mpq[d[c]]=e(d[c])}})();
</script><!-- end Mixpanel -->
MKP;
  }
}
