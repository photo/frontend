<?php
class ApiController extends BaseController
{
  public static function actionDelete($id)
  {
    $status = Action::delete($id);
    if($status)
      return self::success('Action deleted successfully', $id);
    else
      return self::error('Action deletion failure', false);
  }

  public static function actionPost($targetType, $targetId)
  {
    // TODO: randomize and unique
    // TODO: move to an action model
    $params = $_POST;
    $params['targetId'] = $targetId;
    $params['targetType'] = $targetType;
    $id = Action::add($params);

    if($id)
      return self::success("Action {$id} created on {$targetType} {$targetId}", array_merge(array('id' => $id), $params));
    else
      return self::failure("Error creating action {$id} on {$targetType} {$targetId}", false);
  }

  public static function hello()
  {
    return self::success('Hello, world!', $_GET);
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

  public static function photoDelete($id)
  {
    $status = Photo::delete($id);
    if($status)
      return self::success('Photo deleted successfully', $id);
    else
      return self::error('Photo deletion failure', false);
  }

  public static function photoDynamicUrl($id, $width, $height, $options = null)
  {
    return self::success('Url generated successfully', Photo::generateUrlInternal($id, $width, $height, $options));
  }

  /*public static function photoDynamic($id, $hash, $width, $height, $options = null)
  {
    $photo = Photo::generateImage($id, $hash, $width, $height, $options);
    return self::success('', $photo);
  }*/

  public static function photoUpdate($id)
  {
    $photoUpdatedId = Photo::update($id, $_POST);
    return self::success("photo {$id} updated", $photoUpdatedId);
  }

  public static function photoUpload()
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
      $photoId = Photo::upload($_POST['photo'], $attributes);
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

  public static function photos($options = null)
  {
    $options = (array)explode('/', $options);
    $filters = array('sortBy' => 'dateTaken,desc');
    $pageSize = getConfig()->get('site')->pageSize;
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
}
