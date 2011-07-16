<?php
class PhotosController extends BaseController
{
  public static function create($id, $hash, $width, $height, $options = null)
  {
    $args = func_get_args();
    $photo = Photo::generateImage($id, $hash, $width, $height, $options);
    // TODO return 404 graphic
    if($photo)
    {
      header('Content-Type: image/jpeg');
      readfile($photo);
      unlink($photo);
      return;
    }
    echo 'did not work';
  }

  public static function delete($id)
  {
    $delete = getApi()->invoke("/photo/{$id}/delete.json", EpiRoute::httpPost);
    if($delete['result'] !== false)
      getRoute()->redirect('/photos?deleteSuccess');
    else
      getRoute()->redirect('/photos?deleteFailure');
  }

  public static function photo($id, $options = null)
  {
    $apiResp = getApi()->invoke("/photo/{$id}.json", EpiRoute::httpGet);
    if($apiResp['code'] == 200)
    {
      $photo = $apiResp['result'];
      $sizes = array(
        '300x300' => Photo::generateUrlPublic($photo, 300, 300),
        '300x300xBW' => Photo::generateUrlPublic($photo, 300, 300, 'BW'),
        '300x300xCR' => Photo::generateUrlPublic($photo, 300, 300, 'CR'),
        '500x500' => Photo::generateUrlPublic($photo, 500, 500),
        '700x700' => Photo::generateUrlPublic($photo, 700, 700),
        '900x700' => Photo::generateUrlPublic($photo, 900, 900),
        '1280x1280' => Photo::generateUrlPublic($photo, 1280, 1280)
      );
      if($options === null)
      {
        $photo['displayUrl'] = Photo::generateUrlPublic($photo, 800, 800);
      }
      else
      {
        $fragment = Photo::generateFragmentReverse($options);
        $photo['displayUrl'] = Photo::generateUrlPublic($photo, $fragment['width'], $fragment['height'], $fragment['options']);
      }
      getTemplate()->display('template.php', array('body' => getTemplate()->get('photo.php', array('photo' => $photo, 'sizes' => $sizes))));
    }
    else
    {
      echo "Couldn't find photo {$id}"; // TODO
    }
  }

  public static function photos($options = null)
  {
    $photos = getApi()->invoke("/photos{$options}.json");
    $photos = $photos['result'];
    foreach($photos as $key => $val)
      $photos[$key]['thumb'] = Photo::generateUrlPublic($val, 200, 200);

    $pagination = array('requestUri' => $_SERVER['REQUEST_URI'], 'currentPage' => $photos[0]['currentPage'], 
      'pageSize' => $photos[0]['pageSize'], 'totalPages' => $photos[0]['totalPages']);
    $body = getTemplate()->get('photos.php', array('photos' => $photos, 'pagination' => $pagination));
    getTemplate()->display('template.php', array('body' => $body));
  }

  public static function photosByTags($tags)
  {
    
  }

  public static function update($id)
  {
    $status = getApi()->invoke("/photo/{$id}.json", EpiRoute::httpPost);
    // TODO include success/error paramter
    getRoute()->redirect("/photo/{$id}");
  }

  public static function upload()
  {
    $body = getTemplate()->get('upload.php');
    $js = getTemplate()->get('js/upload.js.php');
    getTemplate()->display('template.php', array('body' => $body, 'js' => $js, 'jsFiles' => array('/assets/js/plugins/jquery-ui.widget.js','/assets/js/plugins/jquery.fileupload.js')));
  }

  public static function uploadPost()
  {
    $upload = getApi()->invoke('/photo/upload.json', EpiRoute::httpPost, array('_FILES' => $_FILES, '_POST' => $_POST));
    if($upload['result'])
      getRoute()->redirect('/photos?uploadSuccess');
    else
      getRoute()->redirect('/photos?uploadFailure');
  }
}
