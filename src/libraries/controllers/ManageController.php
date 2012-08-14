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
    // TODO add pagination to albums
    $albumsResp = $this->api->invoke('/albums/list.json', EpiRoute::httpGet, array('_GET' => array('pageSize' => PHP_INT_MAX)));
    $albums = $albumsResp['result'];
    $groupsResp = $this->api->invoke('/groups/list.json');
    $groups = $groupsResp['result'];
    $albumAddForm = $this->template->get(sprintf('%s/manage-album-form.php', $this->config->paths->templates), array('groups' => $groups));
    $bodyTemplate = sprintf('%s/manage-albums.php', $this->config->paths->templates);
    $body = $this->template->get($bodyTemplate, array('albums' => $albums, 'albumAddForm' => $albumAddForm, 'groups' => $groups, 'crumb' => $this->session->get('crumb')));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'manage-apps'));
  }

  public function apps()
  {
    $credentialsResp = $this->api->invoke('/oauth/list.json');
    $credentials = $credentialsResp['result'];
    $pluginsResp = $this->api->invoke('/plugins/list.json');
    $plugins = $pluginsResp['result'];
    $bodyTemplate = sprintf('%s/manage-apps.php', $this->config->paths->templates);
    $body = $this->template->get($bodyTemplate, array('credentials' => $credentials, 'plugins' => $plugins, 'crumb' => $this->session->get('crumb')));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'manage-apps'));
  }

  public function appsCallback()
  {
    $this->route->redirect('/manage/apps?m=app-created');
  }

  public function features()
  {
    $this->route->redirect('/manage/settings');
  }

  public function home()
  {
    $this->route->redirect('/manage/photos');
  }

  public function groups()
  {
    $groupsResp = $this->api->invoke('/groups/list.json');
    $groups = $groupsResp['result'];
    $groupAddForm = $this->template->get(sprintf('%s/manage-group-form.php', $this->config->paths->templates), array('groups' => $groups));
    $bodyTemplate = sprintf('%s/manage-groups.php', $this->config->paths->templates);
    $body = $this->template->get($bodyTemplate, array('groupAddForm' => $groupAddForm, 'groups' => $groups, 'crumb' => getSession()->get('crumb')));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'manage-groups'));
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
    $photosApiParams = array('_GET' => array_merge($_GET, array('returnSizes' => '160x160', 'pageSize' => 18)));
    $photosResp = $this->api->invoke('/photos/list.json', EpiRoute::httpGet, $photosApiParams);
    $photos = $photosResp['result'];

    $pages = array('pages' => array());
    if(!empty($photos))
    {
      $pages['pages'] = $this->utility->getPaginationParams($photos[0]['currentPage'], $photos[0]['totalPages'], $this->config->pagination->pagesToDisplay);
      $pages['currentPage'] = $photos[0]['currentPage'];
      $pages['totalPages'] = $photos[0]['totalPages'];
      $pages['requestUri'] = $_SERVER['REQUEST_URI'];
    }
    $pagination = $this->theme->get('partials/pagination.php', $pages);

    $bodyTemplate = sprintf('%s/manage-photos.php', $this->config->paths->templates);
    $body = $this->template->get($bodyTemplate, array('photos' => $photos, 'pagination' => $pagination, 'crumb' => getSession()->get('crumb')));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'manage'));
  }

  public function settings()
  {
    $params['downloadOriginal'] = $this->config->site->allowOriginalDownload == '1';
    $params['allowDuplicate'] = $this->config->site->allowDuplicate == '1';
    $params['hideFromSearchEngines'] = $this->config->site->hideFromSearchEngines == '1';
    $params['crumb'] = $this->session->get('crumb');
    $bodyTemplate = sprintf('%s/manage-settings.php', $this->config->paths->templates);
    $body = $this->template->get($bodyTemplate, $params);
    $this->theme->display('template.php', array('body' => $body, 'page' => 'manage-settings'));
  }
}
