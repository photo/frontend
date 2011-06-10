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
}
