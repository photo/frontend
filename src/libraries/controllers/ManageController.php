<?php
/**
  * Manage controller for HTML endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ManageController extends BaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
    $this->photo = new Photo;
    $this->theme->setTheme(); // defaults
    // TODO why?
    if(stristr($_SERVER['REQUEST_URI'], '/manage/apps/callback') === false &&
        stristr($_SERVER['REQUEST_URI'], '/manage/password/reset') === false)
      getAuthentication()->requireAuthentication();
  }

  public function albums()
  {
    $this->route->redirect('/albums/list');
  }

  public function apps()
  {
    $this->route->redirect('/manage/settings#apps');
  }

  public function appsCallback()
  {
    $notification = new Notification;
    $notification->add('Your app has been successfully created. Take a look at <a href="#apps">your apps</a>.', Notification::typeFlash);
    $this->route->redirect('/manage/settings#apps');
  }

  public function features()
  {
    $this->route->redirect('/manage/settings#settings');
  }

  public function home()
  {
    $this->route->redirect('/manage/settings');
  }

  public function groups()
  {
    $this->route->redirect('/manage/settings');
  }

  public function passwordReset($token)
  {
    $user = new User;
    $tokenFromDb = $user->getAttribute('passwordToken');
    if($tokenFromDb != $token)
    {
      $this->route->redirect('/?m=token-expired');
      die();
    }

    $bodyTemplate = sprintf('%s/manage-password-reset.php', $this->config->paths->templates);
    $body = $this->template->get($bodyTemplate, array('passwordToken' => $token));
    $this->theme->display('template.php', array('body' => $body, 'page' => null));
  }

  public function photos()
  {
    $this->route->redirect('/photos/list');
  }

  public function settings()
  {
    $credentialsResp = $this->api->invoke('/oauth/list.json');
    $credentials = $credentialsResp['result'];
    $pluginsResp = $this->api->invoke('/plugins/list.json');
    $plugins = $pluginsResp['result'];
    $tokensResp = $this->api->invoke('/tokens/list.json');
    $tokens = $tokensResp['result'];
    $params['downloadOriginal'] = $this->config->site->allowOriginalDownload == '1';
    $params['enableBetaFeatures'] = $this->config->site->enableBetaFeatures == '1';
    $params['allowDuplicate'] = $this->config->site->allowDuplicate == '1';
    $params['hideFromSearchEngines'] = $this->config->site->hideFromSearchEngines == '1';
    $params['decreaseLocationPrecision'] = $this->config->site->decreaseLocationPrecision == '1';
    $params['credentials'] = $credentials;
    $params['plugins'] = $plugins;
    $params['tokens'] = $tokens;
    $params['admins'] = array();
    if(isset($this->config->user->admins))
      $params['admins'] = (array)explode(',', $this->config->user->admins);
    $params['crumb'] = $this->session->get('crumb');
    $bodyTemplate = sprintf('%s/manage-settings.php', $this->config->paths->templates);
    $body = $this->template->get($bodyTemplate, $params);
    $this->theme->display('template.php', array('body' => $body, 'page' => 'manage-settings'));
  }

  public function settingsPost()
  {
    $notification = new Notification;
    $resp = $this->api->invoke('/manage/settings.json', EpiRoute::httpPost);
    if($resp['code'] === 200)
      $notification->add('Your settings were successfully saved.', Notification::typeFlash, Notification::modeConfirm);
    else
      $notification->add('There was a problem updating your settings.', Notification::typeFlash, Notification::modeError);

    $this->route->redirect('/manage/settings');
  }
}
