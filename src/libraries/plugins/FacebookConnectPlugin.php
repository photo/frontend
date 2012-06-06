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

  public function renderFooter()
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
        xfbml      : false  // parse XFBML
      });

      // Additional initialization code here
    };

    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId={$conf->id}";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  </script>
MKP;
  }
}

