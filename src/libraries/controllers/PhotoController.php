<?php
/**
  * Photo controller for HTML endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class PhotoController extends BaseController
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
  }


  /**
    * Create a new version of the photo with ID $id as specified by $width, $height and $options.
    *
    * @param string $id ID of the photo to create a new version of.
    * @param string $hash Hash to validate this request before creating photo.
    * @param int $width The width of the photo to which this URL points.
    * @param int $height The height of the photo to which this URL points.
    * @param int $options The options of the photo wo which this URL points.
    * @return string HTML
    */
  public function create($id, $hash, $width, $height, $options = null)
  {
    $fragment = $this->photo->generateFragment($width, $height, $options);
    // We cannot call the API since this may not be authenticated.
    // Rely on the hash to confirm it was a valid request
    $db = getDb();
    $photo = $db->getPhoto($id);
    if($photo);
    {
      // check if this size exists
      if(isset($photo["path{$fragment}"]) && stristr($photo["path{$fragment}"], "/{$hash}/") === false)
      {
        $url = $this->photo->generateUrlPublic($photo, $width, $height, $options);
        $this->route->redirect($url, 301, true);
        return;
      }
      else
      {
        // TODO, this should call a method in the API
        $photo = $this->photo->generate($id, $hash, $width, $height, $options);
        // TODO return 404 graphic
        if($photo)
        {
          header('Content-Type: image/jpeg');
          readfile($photo);
          unlink($photo);
          return;
        }
      }
    }
    $this->route->run('/error/500');
  }

  /**
    * Delete a photo specified by the ID.
    *
    * @param string $id ID of the photo to be deleted.
    * @return void HTTP redirect
    */
  public function delete($id)
  {
    getAuthentication()->requireAuthentication();
    $delete = $this->api->invoke("/photo/{$id}/delete.json", EpiRoute::httpPost);
    if($delete['code'] !== 200)
      $this->route->redirect('/photos?deleteSuccess');
    else
      $this->route->redirect('/photos?deleteFailure');
  }

  /**
    * Download and output the original source photo
    * Calls the photo model which writes the data to stdout
    *
    * @param string $id ID of the photo to be edited.
    * @return string Standard JSON envelope
    */
  public function download($id, $token = null)
  {
    $isAttachment = !isset($_GET['stream']) || $_GET['stream'] != '1';
    $userObj = new User;
    // the API enforces permissions, we just have to check for download privileges
    if($userObj->isAdmin() || $this->config->site->allowOriginalDownload == 1)
    {
      if($token === null)
        $photoResp = $this->api->invoke(sprintf('/photo/%s/view.json', $id), EpiRoute::httpGet);
      else
        $photoResp = $this->api->invoke(sprintf('/photo/%s/%s/view.json', $id, $token), EpiRoute::httpGet);

      $photo = $photoResp['result'];
      if($photoResp['code'] === 200)
      {
        // Photo::download returns false on failure
        // If no failure assume success and die()
        if($this->photo->download($photo, $isAttachment))
          die();
      }
    }

    $this->route->run('/error/404');
  }

  /**
    * Return makrup for the edit form for the photo by the ID.
    *
    * @param string $id ID of the photo to be edited.
    * @return string HTML
    */
  public function edit($id)
  {
    getAuthentication()->requireAuthentication();
    $resp = $this->api->invoke("/photo/{$id}/edit.json", EpiRoute::httpGet);
    if($resp['code'] === 200)
    {
      $this->theme->display('template.php', array('body' => $resp['result']['markup'], 'page' => 'photo-edit'));
    }
    else
    {
      $this->route->run('/error/404');
    }
  }

  /**
    * Render a list of the user's photos as specified by optional $filterOpts.
    * If $options are present then it will apply those filter rules.
    *
    * @param string $filterOpts Optional options for filtering
    * @return string HTML
    */
  public function list_($filterOpts = null)
  {
    $returnSizes = sprintf('%s,%s', $this->config->photoSizes->thumbnail, $this->config->photoSizes->detail);
    $getParams = array();
    if(!empty($_SERVER['QUERY_STRING']))
      parse_str($_SERVER['QUERY_STRING'], $getParams);

    $additionalParams = array('returnSizes' => $returnSizes, 'sortBy' => 'dateUploaded,desc');

    $isAlbum = strrpos($filterOpts,"album") === 0;

    if($isAlbum)
    {
      if(!isset($getParams['sortBy']))
        $additionalParams['sortBy'] = 'dateTaken,asc';
    }

    $params = array('_GET' => array_merge($additionalParams, $getParams));

    if($filterOpts)
      $photos = $this->api->invoke("/photos/{$filterOpts}/list.json", EpiRoute::httpGet, $params);
    else
      $photos = $this->api->invoke("/photos/list.json", EpiRoute::httpGet, $params);

    $photos = $photos['result'];

    $filterAttributes = array();
    $this->plugin->setData('photos', $photos);
    $this->plugin->setData('page', 'photos');

    $pages = array('pages' => array());
    if(!empty($photos))
    {
      $pages['pages'] = $this->utility->getPaginationParams($photos[0]['currentPage'], $photos[0]['totalPages'], $this->config->pagination->pagesToDisplay);
      $pages['currentPage'] = $photos[0]['currentPage'];
      $pages['totalPages'] = $photos[0]['totalPages'];
      $pages['requestUri'] = $_SERVER['REQUEST_URI'];
    }
    else
    {
      $photos[0]['totalRows'] = 0;
    }

    // TODO we should clean this up somehow
    $album = $tags = null;
    if(preg_match('/album-([^\/]+)/', $filterOpts, $filterMatches))
    {
      $albumResp = $this->api->invoke("/album/{$filterMatches[1]}/view.json", EpiRoute::httpGet);
      $album = $albumResp['result'];
      $filterAttributes[] = 'album';
      $this->plugin->setData('album', $album);
    }
    if(preg_match('/tags-([^\/]+)/', $filterOpts, $filterMatches))
    {
      $tags = (array)explode(',', $filterMatches[1]);
      $filterAttributes[] = 'tags';
      $this->plugin->setData('tags', $tags);
    }

    $this->plugin->setData('filters', $filterAttributes);

    $photoCount = empty($photos) ? 0 : $photos[0]['totalRows'];
    $currentSortParts = (array)explode(',', $params['_GET']['sortBy']);
    $currentSortBy = $params['_GET']['sortBy'];
    $headingHelper = $this->theme->get($this->utility->getTemplate('partials/photos-sub-heading.php'), array('isAlbum' => $isAlbum, 'album' => $album, 'currentSortBy' => $currentSortBy, 'sortParts' => $currentSortParts, 'photoCount' => $photoCount, 'pages' => $pages, 'uri' => $_SERVER['REQUEST_URI']));

    $body = $this->theme->get($this->utility->getTemplate('photos.php'), array('album' => $album, 'tags' => $tags, 'photos' => $photos, 'pages' => $pages, 'options' => $filterOpts, 'headingHelper' => $headingHelper));
    $this->theme->display($this->utility->getTemplate('template.php'), array('body' => $body, 'page' => 'photos', 'album' => $album/* pass album through for header-secondary */));
  }

  /**
    * Update a photo's data in the datastore.
    * Attributes to update are in _POST.
    *
    * @param string $id ID of the photo to update.
    * @return void HTTP redirect
    */
  public function update($id)
  {
    getAuthentication()->requireAuthentication();
    $status = $this->api->invoke("/photo/{$id}/update.json", EpiRoute::httpPost, array('_POST' => $_POST));
    // TODO include success/error paramter
    $this->route->redirect($this->url->photoView($id, null, false));
  }

  /**
    * Display the upload form for photos.
    *
    * @return string HTML
    */
  public function upload()
  {
    getAuthentication()->requireAuthentication();
    $userObj = new User;
    if(!$userObj->isAdmin())
    {
      $this->route->run('/error/403');
      return;
    }
    $this->theme->setTheme(); // defaults
    $crumb = $this->session->get('crumb');
    $template = sprintf('%s/upload.php', $this->config->paths->templates);
    $albumsResp = $this->api->invoke('/albums/list.json', EpiRoute::httpGet, array('_GET' => array('pageSize' => '0')));
    $preferences = array('permission' => $userObj->getAttribute('stickyPermission'));
    $body = $this->template->get($template, array('crumb' => $crumb, 'albums' => $albumsResp['result'], 'licenses' => $this->utility->getLicenses($userObj->getAttribute('stickyLicense')), 'preferences' => $preferences));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'upload'));
  }

  /**
    * Display the upload form for photos.
    *
    * @return string HTML
    */
  public function uploadBeta()
  {
    getAuthentication()->requireAuthentication();
    $userObj = new User;
    if(!$userObj->isAdmin())
    {
      $this->route->run('/error/403');
      return;
    }
    $this->theme->setTheme(); // defaults
    $crumb = $this->session->get('crumb');
    $template = sprintf('%s/upload-beta.php', $this->config->paths->templates);
    $albumsResp = $this->api->invoke('/albums/list.json', EpiRoute::httpGet, array('_GET' => array('pageSize' => '0')));
    $preferences = array('permission' => $userObj->getAttribute('stickyPermission'));
    $body = $this->template->get($template, array('crumb' => $crumb, 'albums' => $albumsResp['result'], 'licenses' => $this->utility->getLicenses($userObj->getAttribute('stickyLicense')), 'preferences' => $preferences));
    $this->theme->display('template.php', array('body' => $body, 'page' => 'upload'));
  }

  /**
    * Update a photo's data in the datastore.
    * Attributes to update are in _POST.
    *
    * @param string $id ID of the photo to update.
    * @return void HTTP redirect
    */
  public function uploadPost()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $upload = $this->api->invoke('/photo/upload.json', EpiRoute::httpPost, array('_FILES' => $_FILES, '_POST' => $_POST));
    if($upload['result'])
      $this->route->redirect('/photos?uploadSuccess');
    else
      $this->route->redirect('/photos?uploadFailure');
  }

  /**
    * Render the photo page for a photo with ID $id.
    * If $options are present then it will render that photo.
    *
    * @param string $id ID of the photo to be deleted.
    * @param string $options Optional options for rendering this photo.
    * @return string HTML
    */
  public function view($id, $options = null)
  {
    if($options === null)
      $apiResp = $this->api->invoke("/photo/{$id}/view.json", EpiRoute::httpGet, array('_GET' => array('actions' => 'true', 'returnSizes' => $this->config->photoSizes->detail)));
    else
      $apiResp = $this->api->invoke("/photo/{$id}/{$options}/view.json", EpiRoute::httpGet, array('_GET' => array('actions' => 'true', 'returnSizes' => $this->config->photoSizes->detail)));

    if($apiResp['code'] === 200)
    {
      $detailDimensions = explode('x', $this->config->photoSizes->detail);
      $nextPreviousParams = array_merge($_GET, array('returnSizes' => $this->config->photoSizes->nextPrevious));
      if(empty($options))
        $apiNextPrevious = $this->api->invoke("/photo/{$id}/nextprevious.json", EpiRoute::httpGet, array('_GET' => $nextPreviousParams));
      else
        $apiNextPrevious = $this->api->invoke("/photo/{$id}/nextprevious/{$options}.json", EpiRoute::httpGet, array('_GET' => $nextPreviousParams));
      $photo = $apiResp['result'];
      $this->plugin->setData('photo', $photo);
      $this->plugin->setData('page', 'photo-detail');
      $photo['previous'] = isset($apiNextPrevious['result']['previous']) ? $apiNextPrevious['result']['previous'] : null;
      $photo['next'] = isset($apiNextPrevious['result']['next']) ? $apiNextPrevious['result']['next'] : null;
      $crumb = $this->session->get('crumb');
      $body = $this->theme->get($this->utility->getTemplate('photo-details.php'), array('photo' => $photo, 'crumb' => $crumb, 'options' => $options));
      $this->theme->display($this->utility->getTemplate('template.php'), array('body' => $body, 'page' => 'photo-details'));
    }
    else
    {
      $this->route->run('/error/404');
    }
  }
}
