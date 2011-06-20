<?php
class PhotosController extends BaseController
{
  public static function create($id, $hash, $width, $height, $options = null)
  {
    $fragment = Photo::generateFragment($width, $height, $options);
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

  public static function home()
  {
    $photos = getApi()->invoke('/photos.json');
    foreach($photos['result'] as $key => $val)
    {
      $resp = getApi()->invoke("/photo/{$val['id']}/url/200x200.json", EpiRoute::httpGet);
      $url = $resp['result'];
      $photos['result'][$key]['thumb'] = $url;
    }
    $body = getTemplate()->get('photos.php', array('photos' => $photos['result']));
    getTemplate()->display('template.php', array('body' => $body));
  }

  public static function upload()
  {
    $body = getTemplate()->get('upload.php');
    getTemplate()->display('template.php', array('body' => $body));
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
