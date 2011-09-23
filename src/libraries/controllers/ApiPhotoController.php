<?php
/**
  * Photo controller for API endpoints
  *
  * This controller does much of the dispatching to the Photo controller for all photo requests.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiPhotoController extends BaseController
{
  /**
    * Delete a photo specified by the ID.
    *
    * @param string $id ID of the photo to be deleted.
    * @return string Standard JSON envelope
    */
  public static function delete($id)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb($_POST['crumb']);
    $status = Photo::delete($id);
    if($status)
      return self::success('Photo deleted successfully', $id);
    else
      return self::error('Photo deletion failure', false);
  }

  /**
    * Get a form to edit a photo specified by the ID.
    *
    * @param string $id ID of the photo to be edited.
    * @return string Standard JSON envelope
    */
  public static function edit($id)
  {
    getAuthentication()->requireAuthentication();
    $resp = getApi()->invoke("/photo/{$id}/view.json", EpiRoute::httpGet);
    $photo = $resp['result'];
    if($photo)
    {
      $template = sprintf('%s/photo-edit.php', getConfig()->get('paths')->templates);
      $license = null;
      if(isset($photo['license']))
        $license = $photo['license'];
      $markup = getTemplate()->get($template, array('photo' => $photo, 'licenses' => Utility::getLicenses($license), 'crumb' => getSession()->get('crumb')));
      return self::success('Photo deleted successfully', array('markup' => $markup));
    }

    return self::error('Photo deletion failure', false);
  }

  /**
   * Remove all the size keys from the photo but the one in list in $sizes
   *
   * @param $photo the photo object to prune.
   * @param array $sizes the sizes to keep.
   * @return the photo
   */
  private static function pruneSizes($photo, $sizes)
  {
    if(isset($sizes) && !empty($sizes))
    {
      foreach($sizes as $size)
      {
        $sizekeys["path{$size}"] = 1;
      }
    }

    foreach($photo as $photoKey => $photoValue)
    {
      if(preg_match('/path(\d+x\d+)/', $photoKey))
      {
        $prune = true;
        if(isset($sizekeys) && isset($sizekeys[$photoKey]))
        {
          $prune = false;
          break;
        }
        if($prune)
        {
          unset($photo[$photoKey]);
        }
      }
    }
    return $photo;
  }

  /**
    * Retrieve the next and previous photo given photo $id
    *
    * @param string $id ID of the photo to be deleted.
    * @return string Standard JSON envelope
    */
  public static function nextPrevious($id, $options = null)
  {
    $nextPrevious = getDb()->getPhotoNextPrevious($id);
    if(!$nextPrevious)
      return self::error('Could not get next/previous photo', false);

    if(isset($_GET['returnSizes']))
    {
      $sizes = (array)explode(',', $_GET['returnSizes']);
    }
    // if specific sizes are requested then make sure we return them
    foreach($nextPrevious as $key => $photo)
    {
      $nextPrevious[$key] = self::pruneSizes($photo, $sizes);
    }

    // if specific sizes are requested then make sure we return them
    if(isset($sizes))
    {
      $protocol = Utility::getProtocol(false);
      if(isset($_GET['protocol']))
        $protocol = $_GET['protocol'];

      foreach($sizes as $size)
      {
        foreach($nextPrevious as $key => $photo)
        {
          $options = Photo::generateFragmentReverse($size);
          $nextPrevious[$key]["path{$size}"] = Photo::generateUrlPublic($photo, $options['width'], $options['height'], $options['options'], $protocol);
        }
      }
    }

    return self::success("Next/previous for photo {$id}", $nextPrevious);
  }

  /**
    * Retrieve a photo from the remote datasource.
    *
    * @param string $id ID of the photo to be deleted.
    * @return string Standard JSON envelope
    */
  public static function photo($id)
  {
    if(isset($_GET['actions']) && $_GET['actions'] == 'true')
      $photo = getDb()->getPhotoWithActions($id);
    else
      $photo = getDb()->getPhoto($id);

    if(!isset($photo['id']))
      return self::notFound("Photo {$id} not found", false);

    // if specific sizes are requested then make sure we return them
    $sizes = array();
    if(isset($_GET['returnSizes']))
    {
      $sizes = (array)explode(',', $_GET['returnSizes']);
    }

    $photo = self::pruneSizes($photo, $sizes);

    if(isset($sizes))
    {
      $protocol = Utility::getProtocol(false);
      if(isset($_GET['protocol']))
        $protocol = $_GET['protocol'];

      foreach($sizes as $size)
      {
        $options = Photo::generateFragmentReverse($size);
        $photo["path{$size}"] = Photo::generateUrlPublic($photo, $options['width'], $options['height'], $options['options'], $protocol);
      }
    }

    return self::success("Photo {$id}", $photo);
  }

  /**
    * Given a photo ID generate a url with the specified $width, $height and $options
    *
    * @param string $id ID of the photo to be deleted.
    * @param int $width The width of the photo to which this URL points.
    * @param int $height The height of the photo to which this URL points.
    * @param int $options The options of the photo wo which this URL points.
    * @return string Standard JSON envelope
    */
  public static function dynamicUrl($id, $width, $height, $options = null)
  {
    return self::success('Url generated successfully', Photo::generateUrlInternal($id, $width, $height, $options));
  }

  /*public static function dynamic($id, $hash, $width, $height, $options = null)
  {
    $photo = Photo::generateImage($id, $hash, $width, $height, $options);
    return self::success('', $photo);
  }*/

  /**
    * Retrieve a list of the user's photos from the remote datasource.
    * The $filterOpts are values from the path but can also be in _GET.
    * /photos/page-2/tags-favorites.json is identical to /photos.json?page=2&tags=favorites
    *
    * @param string $filterOpts Options on how to filter the list of photos.
    * @return string Standard JSON envelope
    */
  public static function photos($filterOpts = null)
  {
    // If the user is logged in then we can display photos based on group membership
    $permission = 0;
    if(User::isOwner())
    {
      $permission = 1;
    }
    elseif(User::isLoggedIn())
    {
      $userGroups = User::getGroups(User::getEmailAddress());
      if(!empty($userGroups))
      {
        $permission = -1;
        $groupIds = array();
        foreach($userGroups as $group)
          $groupIds[] = $group['id'];
      }

    }
    // This section enables in path parameters which are normally GET
    $pageSize = getConfig()->get('site')->pageSize;
    $filters = array('sortBy' => 'dateTaken,desc');
    if($filterOpts !== null)
    {
      $filterOpts = (array)explode('/', $filterOpts);
      foreach($filterOpts as $value)
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
            $sortOptions = (array)explode(',', $value);
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
    if(isset($_GET['pageSize']) && intval($_GET['pageSize']) == $_GET['pageSize'])
      $pageSize = intval($_GET['pageSize']);
    $filters = array_merge($filters, $_GET);

    $page = 1;
    if(isset($filters['page']))
      $page = $filters['page'];
    $protocol = Utility::getProtocol(false);
    if(isset($filters['protocol']))
      $protocol = $filters['protocol'];

    if($permission == 0 || $permission = 1)
      $filters['permission'] = $permission;
    else
      $filters['groups'] = $groups;

    $db = getDb();
    $photos = $db->getPhotos($filters, $pageSize);
    if($photos)
    {
      if(isset($filters['returnSizes']))
        $sizes = (array)explode(',', $filters['returnSizes']);

      foreach($photos as $key => $photo)
      {
        // we remove all path* entries to keep the interface clean and only return sizes explicitly requested
        // we need to leave the 'locally scoped' $photo in since we may put it back into the $photos array if requested
        $photos[$key] = self::pruneSizes($photo, $sizes);

        if(isset($sizes))
        {
          foreach($sizes as $size)
          {
            // TODO call API
            // here we put a previously deleted key back in - ah, the things we do for consistency
            $options = Photo::generateFragmentReverse($size);
            $photos[$key]["path{$size}"] = Photo::generateUrlPublic($photo, $options['width'], $options['height'], $options['options'], $protocol);
          }
        }
      }
    }
    else
    {
      return self::error('Could not retrieve photos', false);
    }

    $photos[0]['pageSize'] = $pageSize;
    $photos[0]['currentPage'] = $page;
    $photos[0]['totalPages'] = ceil($photos[0]['totalRows'] / $pageSize);
    return self::success("Successfully retrieved user's photos", $photos);
  }

  /**
    * Upload a photo.
    * This stores the original photo plus a base version used for future manipulations.
    * If a returnoptions value is present then a version using that value is also generated.
    * This stores in the remote file system as well as the remote data store.
    * Parameters are contained in _POST.
    *
    * @return string standard json envelope
    */
  public static function upload()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb($_POST['crumb']);
    $attributes = $_POST;
    if(isset($attributes['returnSizes']))
    {
      $returnSizes = $attributes['returnSizes'];
      unset($attributes['returnSizes']);
    }
    if(isset($attributes['crumb']))
    {
      unset($attributes['crumb']);
    }

    $photoId = false;
    // TODO call API
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
      if(isset($returnSizes))
      {
        $sizes = (array)explode(',', $returnSizes);
        foreach($sizes as $size)
        {
          $options = Photo::generateFragmentReverse($size);
          $hash = Photo::generateHash($photoId, $options['width'], $options['height'], $options['options']);
          Photo::generateImage($photoId, $hash, $options['width'], $options['height'], $options['options']);
        }
      }

      if(isset($_POST['tags']) && !empty($_POST['tags']))
      {
        $tags = (array)explode(',', $_POST['tags']);
        Tag::updateTagCounts(array(), $tags);
      }
      $params = array();
      if(isset($returnSizes))
        $params = array('returnSizes' => $returnSizes);
      $photo = getApi()->invoke("/photo/{$photoId}/view.json", EpiRoute::httpGet, array('_GET' => $params));
      return self::created("Photo {$photoId} uploaded successfully", $photo['result']);
    }

    return self::error('File upload failure', false);
  }

  /**
    * Update the data associated with the photo in the remote data store.
    * Parameters to be updated are in _POST
    * This method also manages updating tag counts
    *
    * @param string $id ID of the photo to be updated.
    * @return string Standard JSON envelope
    */
  public static function update($id)
  {
    getAuthentication()->requireAuthentication();
    // diff/manage tag counts - not critical
    if(isset($_POST['tags']) && !empty($_POST['tags']))
    {
      $photo = getApi()->invoke("/photo/{$id}/view.json", EpiRoute::httpGet);
      if($photo)
      {
        $existingTags = $photo['result']['tags'];
        $updatedTags = (array)explode(',', $_POST['tags']);
        Tag::updateTagCounts($existingTags, $updatedTags);
      }
    }
    if(isset($_POST['crumb']))
    {
      unset($_POST['crumb']);
    }
    $photoUpdatedId = Photo::update($id, $_POST);
    return self::success("photo {$id} updated", $photoUpdatedId);
  }
}
