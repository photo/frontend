<?php
class PhotosController extends BaseController
{
  public static function home()
  {
    $photos = getApi()->invoke('/photos.json');
    echo '<ul>';
    foreach($photos['result'] as $photo)
    {
      echo "<li>Photo {$photo->id} has url {$photo->urlOriginal}</li>";
    }
    echo '</ul>';
  }

  public static function upload()
  {
    $upload = getApi()->invoke('/photo/upload.json');
    if($upload)
      echo '<h1>Your file was uploaded successfully</h1>';
    else
      echo '<h1>Sorry, there was a problem uploading your file</h1>';
  }
}
