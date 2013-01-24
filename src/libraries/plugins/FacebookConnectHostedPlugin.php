<?php
/**
 * FacebookConnectHosted
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FacebookConnectHostedPlugin extends PluginBase
{
  private $id, $secret, $configObj;
  public function __construct()
  {
    parent::__construct();
    $this->configObj = getConfig()->get();
  }

  public function defineConf()
  {
    return array('id' => $this->configObj->facebookConnect->id, 'secret' => $this->configObj->facebookConnect->secret);
  }

  public function renderFooter()
  {
    parent::renderFooter();
    $conf = $this->getConf();
    return <<<MKP
  <div id="fb-root"></div>
  <script>
    window.fbAsyncInit = function() {
      FB.init({ appId: '{$conf->id}', status: true, cookie: true, oauth: true, xfbml: true });
      FB.Event.subscribe('edge.create',
        function(response) {
          _gaq.push(['_trackEvent', 'FBLike', 'like', response]);
        }
      );
      FB.Event.subscribe('edge.remove',
        function(response) {
          _gaq.push(['_trackEvent', 'FBLike', 'unlike', response]);
        }
      );
    };

    // Load the SDK Asynchronously
    (function(d){
       var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
       js = d.createElement('script'); js.id = id; js.async = true;
       js.src = "//connect.facebook.net/en_US/all.js";
       d.getElementsByTagName('head')[0].appendChild(js);
     }(document));
  </script>
MKP;
  }
}
