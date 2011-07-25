<?php
class ApiPhotoController extends BaseController
{
  public static function delete($id)
  {
    $status = Photo::delete($id);
    if($status)
      return self::success('Photo deleted successfully', $id);
    else
      return self::error('Photo deletion failure', false);
  }

  public static function photo($id)
  {
    if(isset($_GET['actions']) && $_GET['actions'] == 'true')
      $photo = getDb()->getPhotoWithActions($id);
    else
      $photo = getDb()->getPhoto($id);

    if($photo)
      return self::success("Photo {$id}", $photo);
    else
      return self::notFound("Photo {$id} not found", false);
  }

  public static function dynamicUrl($id, $width, $height, $options = null)
  {
    return self::success('Url generated successfully', Photo::generateUrlInternal($id, $width, $height, $options));
  }

  /*public static function dynamic($id, $hash, $width, $height, $options = null)
  {
    $photo = Photo::generateImage($id, $hash, $width, $height, $options);
    return self::success('', $photo);
  }*/

  public static function photos($options = null)
  {
    // This section enables in path parameters which are normally GET
    $pageSize = getConfig()->get('site')->pageSize;
    $filters = array('sortBy' => 'dateTaken,desc');
    if($options !== null)
    {
      $options = (array)explode('/', $options);
      foreach($options as $value)
      {
        $parts = explode('-', $value);
        if(count($parts) != 2)
          continue;

        switch($parts[0])
        {
          case 'pageSize':
            $pageSize = intval($parts[1]);
            break;
          case 'sortBy':
            $sortOptions = explode(',', $value);
            if(count($sortOptions) != 2 || preg_match('/[^a-zA-Z0-9,]/', $parts[1]))
              continue;
            $filters[$parts[0]] = $parts[1];
            break;
          default:
            $filters[$parts[0]] = $parts[1];
            break;
        }
      }
    }
    // merge path parameters with GET parameters. GET parameters override
    $filters = array_merge($filters, $_GET);

    $page = 1;
    if(isset($filters['page']))
      $page = $filters['page'];
    $db = getDb();
    $photos = $db->getPhotos($filters, $pageSize);
    if(!$photos)
      return self::error('Could not retrieve photos', false);

    $photos[0]['pageSize'] = $pageSize;
    $photos[0]['currentPage'] = $page;
    $photos[0]['totalPages'] = ceil($photos[0]['totalRows'] / $pageSize);
    return self::success('', $photos);
  }

  public static function upload()
  {
    $attributes = $_POST;
    if(isset($attributes['returnOptions']))
    {
      $returnOptions = $attributes['returnOptions'];
      unset($attributes['returnOptions']);
    }

    $photoId = false;
    if(isset($_FILES) && isset($_FILES['photo']))
    {
      $photoId = Photo::upload($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $attributes);
    }
    elseif(isset($_POST['photo']))
    {
      unset($attributes['photo']);
      $localFile = tempnam(getConfig()->get('server')->tempDir, 'opme');
      $name = basename($localFile).'.jpg';
      file_put_contents($localFile, base64_decode($_POST['photo']));
      $photoId = Photo::upload($localFile, $name, $attributes);
    }

    if($photoId)
    {
      $returnPhotoSuccess = false;
      if(isset($returnOptions))
      {
        $options = Photo::generateFragmentReverse($returnOptions);
        $hash = Photo::generateHash($photoId, $options['width'], $options['height'], $options['options']);
        $returnPhotoSuccess = Photo::generateImage($photoId, $hash, $options['width'], $options['height'], $options['options']);
      }

      if($photoId)
      {
        $photo = getDb()->getPhoto($photoId);
        if($returnPhotoSuccess)
          $photo['requestedUrl'] = $photo["path{$returnOptions}"];
        return self::created("Photo {$photoId} uploaded successfully", $photo);
      }
    }

    return self::error('File upload failure', false);
  }

  public static function update($id)
  {
    $photoUpdatedId = Photo::update($id, $_POST);
    return self::success("photo {$id} updated", $photoUpdatedId);
  }
}
