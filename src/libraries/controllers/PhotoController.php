<?php
/**
  * Photo controller for HTML endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class PhotoController extends BaseController
{
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
  public static function create($id, $hash, $width, $height, $options = null)
  {
    $args = func_get_args();
    // TODO, this should call a method in the API
    $photo = Photo::generateImage($id, $hash, $width, $height, $options);
    // TODO return 404 graphic
    if($photo)
    {
      header('Content-Type: image/jpeg');
      readfile($photo);
      unlink($photo);
      return;
    }
    getRoute()->run('/error/500');
  }

  /**
    * Delete a photo specified by the ID.
    *
    * @param string $id ID of the photo to be deleted.
    * @return void HTTP redirect
    */
  public static function delete($id)
  {
    getAuthentication()->requireAuthentication();
    $delete = getApi()->invoke("/photo/{$id}/delete.json", EpiRoute::httpPost);
    if($delete['code'] !== 200)
      getRoute()->redirect('/photos?deleteSuccess');
    else
      getRoute()->redirect('/photos?deleteFailure');
  }

  /**
    * Return makrup for the edit form for the photo by the ID.
    *
    * @param string $id ID of the photo to be edited.
    * @return string HTML
    */
  public static function edit($id)
  {
    getAuthentication()->requireAuthentication();
    $resp = getApi()->invoke("/photo/{$id}/edit.json", EpiRoute::httpGet);
    if($resp['code'] === 200)
    {
      getTheme()->display('template.php', array('body' => $resp['result']['markup'], 'page' => 'photo-edit'));
    }
    else
    {
      getRoute()->run('/error/404');
    }
  }

  /**
    * Render the photo page for a photo with ID $id.
    * If $options are present then it will render that photo.
    *
    * @param string $id ID of the photo to be deleted.
    * @param string $options Optional options for rendering this photo.
    * @return string HTML
    */
  public static function photo($id, $options = null)
  {
    $apiResp = getApi()->invoke("/photo/{$id}/view.json", EpiRoute::httpGet, array('_GET' => array('actions' => 'true', 'returnSizes' => getConfig()->get('photoSizes')->detail)));
    if($apiResp['code'] === 200)
    {
      $detailDimensions = explode('x', getConfig()->get('photoSizes')->detail);
      if(empty($options))
        $apiNextPrevious = getApi()->invoke("/photo/{$id}/nextprevious.json", EpiRoute::httpGet, array('_GET' => array('returnSizes' => getConfig()->get('photoSizes')->nextPrevious)));
      else
        $apiNextPrevious = getApi()->invoke("/photo/{$id}/nextprevious/{$options}.json", EpiRoute::httpGet, array('_GET' => array('returnSizes' => getConfig()->get('photoSizes')->nextPrevious)));
      $photo = $apiResp['result'];
      if($photo['width'] <= $detailDimensions[0])
      {
        $photo['thisWidth'] = $photo['width'];
        $photo['thisHeight'] = $photo['height'];
      }
      else
      {
        $photo['thisWidth'] = $detailDimensions[0];
        $photo['thisHeight'] = intval($photo['height']/$photo['width']*$detailDimensions[0]);
      }
      $photo['previous'] = isset($apiNextPrevious['result']['previous']) ? $apiNextPrevious['result']['previous'] : null;
      $photo['next'] = isset($apiNextPrevious['result']['next']) ? $apiNextPrevious['result']['next'] : null;
      $crumb = getSession()->get('crumb');
      $body = getTheme()->get('photo-details.php', array('photo' => $photo, 'crumb' => $crumb, 'options' => $options));
      getTheme()->display('template.php', array('body' => $body, 'page' => 'photo-details'));
    }
    else
    {
      getRoute()->run('/error/404');
    }
  }

  /**
    * Render a list of the user's photos as specified by optional $filterOpts.
    * If $options are present then it will apply those filter rules.
    *
    * @param string $filterOpts Optional options for filtering
    * @return string HTML
    */
  public static function photos($filterOpts = null)
  {
    if($filterOpts)
      $photos = getApi()->invoke("/photos/{$filterOpts}/view.json", EpiRoute::httpGet, array('_GET' => array('returnSizes' => getConfig()->get('photoSizes')->thumbnail)));
    else
      $photos = getApi()->invoke("/photos/view.json", EpiRoute::httpGet, array('_GET' => array('returnSizes' => getConfig()->get('photoSizes')->thumbnail)));

    $photos = $photos['result'];

    $pagination = array('requestUri' => $_SERVER['REQUEST_URI'], 'currentPage' => $photos[0]['currentPage'],
      'pageSize' => $photos[0]['pageSize'], 'totalPages' => $photos[0]['totalPages']);


    $body = getTheme()->get('photos.php', array('photos' => $photos, 'pagination' => $pagination, 'options' => $filterOpts));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'photos'));
  }

  /**
    * Update a photo's data in the datastore.
    * Attributes to update are in _POST.
    *
    * @param string $id ID of the photo to update.
    * @return void HTTP redirect
    */
  public static function update($id)
  {
    getAuthentication()->requireAuthentication();
    $status = getApi()->invoke("/photo/{$id}/update.json", EpiRoute::httpPost, array('_POST' => $_POST));
    // TODO include success/error paramter
    getRoute()->redirect(Url::photoView($id, null, false));
  }

  /**
    * Display the upload form for photos.
    *
    * @return string HTML
    */
  public static function upload()
  {
    if(!User::isOwner())
    {
      getRoute()->run('/error/403');
      return;
    }
    $crumb = getSession()->get('crumb');
    $body = getTheme()->get('upload.php', array('crumb' => $crumb, 'licenses' => Utility::getLicenses()));
    getTheme()->display('template.php', array('body' => $body, 'page' => 'upload'));
  }

  /**
    * Update a photo's data in the datastore.
    * Attributes to update are in _POST.
    *
    * @param string $id ID of the photo to update.
    * @return void HTTP redirect
    */
  public static function uploadPost()
  {
    getAuthentication()->requireAuthentication();
    $upload = getApi()->invoke('/photo/upload.json', EpiRoute::httpPost, array('_FILES' => $_FILES, '_POST' => $_POST));
    if($upload['result'])
      getRoute()->redirect('/photos?uploadSuccess');
    else
      getRoute()->redirect('/photos?uploadFailure');
  }
}
