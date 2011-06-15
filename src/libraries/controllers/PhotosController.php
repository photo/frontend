<?php
class PhotosController extends BaseController
{
  public static function home()
  {
    $photos = getApi()->invoke('/photos.json');
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
    $upload = getApi()->invoke('/photo/upload.json');
    if($upload)
      echo '<h1>Your file was uploaded successfully</h1>';
    else
      echo '<h1>Sorry, there was a problem uploading your file</h1>';
  }
}
