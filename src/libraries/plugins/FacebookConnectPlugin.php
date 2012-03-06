<?php
/**
 * FacebookConnect
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FacebookConnectPlugin extends PluginBase
{
  private $id, $secret;
  public function __construct()
  {
    parent::__construct();
  }

  public function defineConf()
  {
    return array('id' => null, 'secret' => null);
  }

  public function renderFooter($params = null)
  {
    parent::renderFooter();
    $conf = $this->getConf();
    return <<<MKP
  <div id="fb-root"></div>
  <script>
    window.fbAsyncInit = function() {
      FB.init({
        appId      : '{$conf->id}', // App ID
        status     : true, // check login status
        cookie     : true, // enable cookies to allow the server to access the session
        oauth      : true, // enable OAuth 2.0
        xfbml      : true  // parse XFBML
      });

      // Additional initialization code here
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

