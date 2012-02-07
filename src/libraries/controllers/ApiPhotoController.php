<?php
/**
  * Photo controller for API endpoints
  *
  * This controller does much of the dispatching to the Photo controller for all photo requests.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class ApiPhotoController extends ApiBaseController
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
    $this->tag = new Tag;
    $this->user = new User;
  }

  /**
    * Delete a photo specified by the ID.
    *
    * @param string $id ID of the photo to be deleted.
    * @return string Standard JSON envelope
    */
  public function delete($id)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $res = $this->api->invoke("/photo/{$id}/view.json");
    $status = $this->photo->delete($id);
    if($status)
    {
      $this->tag->updateTagCounts($res['result']['tags'], array(), 1, 1);
      return $this->noContent('Photo deleted successfully', true);
    }
    else
    {
      return $this->error('Photo deletion failure', false);
    }
  }

  /**
    * Delete multiple photos
    *
    * @return string Standard JSON envelope
    */
  public function deleteBatch()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    if(!isset($_POST['ids']) || empty($_POST['ids']))
      return $this->error('This API requires an ids parameter.', false);

    $ids = (array)explode(',', $_POST['ids']);
    $params = $_POST;
    unset($params['ids']);

    $retval = true;
    foreach($ids as $id)
    {
      $response = $this->api->invoke("/photo/{$id}/delete.json", EpiRoute::httpPost, array('_POST' => $params));
      $retval = $retval && $response['result'] !== false;
    }

    if($retval)
      return $this->noContent(sprintf('%d photos deleted', count($ids)), true);
    else
      return $this->error('Error deleting one or more photos', false);
  }

  /**
    * Get a form to edit a photo specified by the ID.
    *
    * @param string $id ID of the photo to be edited.
    * @return string Standard JSON envelope
    */
  public function edit($id)
  {
    getAuthentication()->requireAuthentication();
    $photoResp = $this->api->invoke("/photo/{$id}/view.json", EpiRoute::httpGet);
    $groupsResp = $this->api->invoke('/groups/list.json', EpiRoute::httpGet);
    $photo = $photoResp['result'];
    $groups = $groupsResp['result'];
    if(!$groups)
      $groups = array();
    if($photo)
    {
      $template = sprintf('%s/photo-edit.php', $this->config->paths->templates);
      $license = null;
      if(isset($photo['license']))
        $license = $photo['license'];
      $this->template->url = new Url;
      $this->template->utility = new Utility;
      $markup = $this->template->get($template, array('photo' => $photo, 'groups' => $groups, 'licenses' => $this->utility->getLicenses($license), 'crumb' => getSession()->get('crumb')));
      return $this->success('Photo edit markup', array('markup' => $markup));
    }

    return $this->error('Photo edit markup failure', false);
  }

  /**
    * Retrieve the next and previous photo given photo $id
    *
    * @param string $id ID of the photo to be deleted.
    * @return string Standard JSON envelope
    */
  public function nextPrevious($id, $filterOpts = null)
  {
    extract($this->parseFilters($filterOpts));
    $nextPrevious = getDb()->getPhotoNextPrevious($id, $filters);
    if(!$nextPrevious)
      return $this->error('Could not get next/previous photo', false);

    $sizes = array();
    if(isset($_GET['returnSizes']))
      $sizes = (array)explode(',', $_GET['returnSizes']);

    foreach($nextPrevious as $key => $photo)
      $nextPrevious[$key] = $this->pruneSizes($photo, $sizes);

    $generate = $requery = false;
    if(isset($_GET['generate']) && $_GET['generate'] == 'true')
      $generate = true;

    // if specific sizes are requested then make sure we return them
    if(!empty($sizes))
    {
      $protocol = $this->utility->getProtocol(false);
      if(isset($_GET['protocol']))
        $protocol = $_GET['protocol'];

      foreach($sizes as $size)
      {
        foreach($nextPrevious as $key => $photo)
        {
          $options = $this->photo->generateFragmentReverse($size);
          if($generate && !isset($nextPrevious[$key]["path{$size}"]))
          {
            $hash = $this->photo->generateHash($photo['id'], $options['width'], $options['height'], $options['options']);
            $this->photo->generate($photo['id'], $hash, $options['width'], $options['width'], $options['options']);
            $requery = true;
          }
        }
      }

      // requery to get generated paths
      if($requery)
      {
        $nextPrevious = getDb()->getPhotoNextPrevious($id, $filters);
        foreach($nextPrevious as $key => $photo)
          $nextPrevious[$key] = $this->pruneSizes($photo, $sizes);
      }
    }

    foreach($nextPrevious as $key => $photo)
      $nextPrevious[$key] = $this->photo->addApiUrls($photo, $sizes);

    return $this->success("Next/previous for photo {$id}", $nextPrevious);
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
  public function dynamicUrl($id, $width, $height, $options = null)
  {
    return $this->success('Url generated successfully', $this->photo->generateUrlInternal($id, $width, $height, $options));
  }

  /*public function dynamic($id, $hash, $width, $height, $options = null)
  {
    $photo = $this->photo->generate($id, $hash, $width, $height, $options);
    return $this->success('', $photo);
  }*/

  /**
    * Retrieve a list of the user's photos from the remote datasource.
    * The $filterOpts are values from the path but can also be in _GET.
    * /photos/page-2/tags-favorites.json is identical to /photos.json?page=2&tags=favorites
    *
    * @param string $filterOpts Options on how to filter the list of photos.
    * @return string Standard JSON envelope
    */
  public function list_($filterOpts = null)
  {
    // this extracts local variables $permission, $filter, $pageSize, etc
    extract($this->parseFilters($filterOpts));
    $db = getDb();
    $photos = $db->getPhotos($filters, $pageSize);

    if(empty($photos))
      return $this->success('Your search did not return any photos', null);

    $sizes = array();
    if(isset($filters['returnSizes']))
      $sizes = (array)explode(',', $filters['returnSizes']);

    $generate = $requery = false;
    if(isset($_GET['generate']) && $_GET['generate'] == 'true')
      $generate = true;

    if($photos[0]['totalRows'] > 0)
    {
      foreach($photos as $key => $photo)
      {
        // we remove all path* entries to keep the interface clean and only return sizes explicitly requested
        // we need to leave the 'locally scoped' $photo in since we may put it back into the $photos array if requested
        $photos[$key] = $this->pruneSizes($photo, $sizes);

        if(!empty($sizes))
        {
          foreach($sizes as $size)
          {
            // TODO call API
            // we do this to put a previously deleted key (pruneSizes) back in - ah, the things we do for consistency
            $options = $this->photo->generateFragmentReverse($size);
            if($generate && !isset($photo["path{$size}"]))
            {
              $hash = $this->photo->generateHash($photo['id'], $options['width'], $options['height'], $options['options']);
              $this->photo->generate($photo['id'], $hash, $options['width'], $options['height'], $options['options']);
              $requery = true;
            }
          }
        }
      }

      // requery to get generated paths
      if($requery)
      {
        $photos = $db->getPhotos($filters, $pageSize);
        foreach($photos as $key => $photo)
          $photos[$key] = $this->pruneSizes($photo, $sizes);
      }

      // we have to merge to retain multiple sizes else the last one overwrites the rest
      // we also can't pass in $photo since it doesn't persist over iterations and removes returnSizes
      foreach($photos as $key => $photo)
        $photos[$key] = $this->photo->addApiUrls($photos[$key], $sizes);
    }

    $photos[0]['pageSize'] = $pageSize;
    $photos[0]['currentPage'] = $page;
    $photos[0]['totalPages'] = ceil($photos[0]['totalRows'] / $pageSize);
    return $this->success("Successfully retrieved user's photos", $photos);
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
  public function upload()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $httpObj = new Http;
    $attributes = $_REQUEST;
    if(isset($attributes['__route__']))
      unset($attributes['__route__']);

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
      $photoId = $this->photo->upload($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $attributes);
    }
    elseif(isset($_POST['photo']))
    {
      // if a filename is passed in we use it else it's the random temp name
      $localFile = tempnam($this->config->paths->temp, 'opme');
      if(isset($_POST['filename']))
        $name = $_POST['filename'];
      else
        $name = basename($localFile).'.jpg';

      // if we have a path to a photo we download it
      // else we base64_decode it
      if(preg_match('#https?://#', $_POST['photo']))
      {
        unset($attributes['photo']);
        $fp = fopen($localFile, 'w');
        $ch = curl_init($_POST['photo']);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        $photoId = $this->photo->upload($localFile, $name, $attributes);
      }
      else
      {
        unset($attributes['photo']);
        file_put_contents($localFile, base64_decode($_POST['photo']));
        $photoId = $this->photo->upload($localFile, $name, $attributes);
      }
    }

    if($photoId)
    {
      if(isset($returnSizes))
      {
        $sizes = (array)explode(',', $returnSizes);
        foreach($sizes as $size)
        {
          $options = $this->photo->generateFragmentReverse($size);
          $hash = $this->photo->generateHash($photoId, $options['width'], $options['height'], $options['options']);
          $this->photo->generate($photoId, $hash, $options['width'], $options['height'], $options['options']);
        }
      }

      $params = array();
      if(isset($returnSizes))
        $params = array('returnSizes' => $returnSizes);
      $photo = $this->api->invoke("/photo/{$photoId}/view.json", EpiRoute::httpGet, array('_GET' => $params));

      $webhookApi = $this->api->invoke('/webhooks/photo.upload/list.json', EpiRoute::httpGet);
      if(!empty($webhookApi['result']) && is_array($webhookApi['result']))
      {
        $photoAsArgs = $photo['result'];
        $photoAsArgs['tags'] = implode(',', $photoAsArgs['tags']);
        foreach($webhookApi['result'] as $key => $hook)
        {
          $httpObj->fireAndForget($hook['callback'], 'POST', $photoAsArgs);
          $this->logger->info(sprintf('Webhook callback executing for photo.upload: %s', $hook['callback']));
        }
      }
      return $this->created("Photo {$photoId} uploaded successfully", $photo['result']);
    }

    return $this->error('File upload failure', false);
  }

  /**
    * Update the data associated with the photo in the remote data store.
    * Parameters to be updated are in _POST
    * This method also manages updating tag counts
    *
    * @param string $id ID of the photo to be updated.
    * @return string Standard JSON envelope
    */
  public function update($id)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    // diff/manage tag counts - not critical
    $params = $_POST;
    if(isset($params['tags']) || isset($params['tagsAdd']) || isset($params['tagsRemove']))
    {
      $photoBefore = $this->api->invoke("/photo/{$id}/view.json", EpiRoute::httpGet);
      $photoBefore = $photoBefore['result'];
      if($photoBefore)
      {
        $existingTags = $photoBefore['tags'];
        $updatedTags = array();
        if(isset($params['tags']))
        {
          $updatedTags = array_merge($updatedTags, (array)explode(',', $params['tags']));
        }
        else
        {
          $updatedTags = $existingTags;
          if(isset($params['tagsAdd']))
          {
            $updatedTags = array_merge($updatedTags, (array)explode(',', $params['tagsAdd']));
            unset($params['tagsAdd']);
          }
          if(isset($params['tagsRemove']))
          {
            $updatedTags = array_diff($updatedTags, (array)explode(',', $params['tagsRemove']));
            unset($params['tagsRemove']);
          }
          $params['tags'] = implode(',', $updatedTags);
        }

        $permission = $photoBefore['permission'];
        if(isset($params['permission']))
          $permission = $params['permission'];
        $this->tag->updateTagCounts($existingTags, $updatedTags, $permission, $photoBefore['permission']);
      }
    }

    if(isset($params['crumb']))
      unset($params['crumb']);

    $photoUpdatedId = $this->photo->update($id, $params);

    if($photoUpdatedId)
    {
      $photo = $this->api->invoke("/photo/{$id}/view.json", EpiRoute::httpGet);
      return $this->success("photo {$id} updated", $photo['result']);
    }

    return $this->error("photo {$id} could not be updated", false);
  }

  /**
    * Update the data associated with the photo in the remote data store.
    * Parameters to be updated are in _POST
    * This method also manages updating tag counts
    *
    * @return string Standard JSON envelope
    */
  public function updateBatch()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    if(!isset($_POST['ids']) || empty($_POST['ids']))
      return $this->error('This API requires an ids parameter.', false);

    $ids = (array)explode(',', $_POST['ids']);
    $params = $_POST;
    unset($params['ids']);

    $retval = true;
    foreach($ids as $id)
    {
      $response = $this->api->invoke("/photo/{$id}/update.json", EpiRoute::httpPost, array('_POST' => $params));
      $retval = $retval && $response['result'] !== false;
    }

    if($retval)
      return $this->success(sprintf('%d photos updated', count($ids)), true);
    else
      return $this->error('Error updating one or more photos', false);
  }

  /**
    * Retrieve a photo from the remote datasource.
    *
    * @param string $id ID of the photo to be viewed.
    * @return string Standard JSON envelope
    */
  public function view($id)
  {
    $getActions = isset($_GET['actions']) && $_GET['actions'] == 'true';
    if($getActions)
      $photo = getDb()->getPhotoWithActions($id);
    else
      $photo = getDb()->getPhoto($id);

    // check permissions
    if(!isset($photo['id']))
    {
      return $this->notFound("Photo {$id} not found", false);
    }
    elseif(!$this->user->isOwner())
    {
      if($photo['permission'] == 0)
      {
        if(!$this->user->isLoggedIn() || (isset($photo['groups']) && empty($photo['groups'])))
          return $this->notFound("Photo {$id} not found", false);

        // can't call API since we're not the owner
        $userGroups = getDb()->getGroups($this->user->getEmailAddress());
        $isInGroup = false;
        foreach($userGroups as $group)
        {
          if(in_array($group['id'], $photo['groups']))
          {
            $isInGroup = true;
            break;;
          }
        }

        if(!$isInGroup)
          return $this->notFound("Photo {$id} not found", false);
      }
    }

    // if specific sizes are requested then make sure we return them
    $sizes = array();
    if(isset($_GET['returnSizes']))
    {
      $sizes = (array)explode(',', $_GET['returnSizes']);
    }

    $photo = $this->pruneSizes($photo, $sizes);

    if(!empty($sizes))
    {
      $protocol = $this->utility->getProtocol(false);
      if(isset($_GET['protocol']))
        $protocol = $_GET['protocol'];

      $generate = $requery = false;
      if(isset($_GET['generate']) && $_GET['generate'] == 'true')
        $generate = true;

      foreach($sizes as $size)
      {
        $options = $this->photo->generateFragmentReverse($size);
        if($generate && !isset($photo["path{$size}"]))
        {
          $hash = $this->photo->generateHash($id, $options['width'], $options['height'], $options['options']);
          $this->photo->generate($id, $hash, $options['width'], $options['width'], $options['options']);
          $requery = true;
        }
      }

      // requery to get generated paths
      if($requery)
      {
        if($getActions)
          $photo = getDb()->getPhotoWithActions($id);
        else
          $photo = getDb()->getPhoto($id);

        $photo = $this->pruneSizes($photo, $sizes);
      }
    }

    $photo = $this->photo->addApiUrls($photo, $sizes);
    return $this->success("Photo {$id}", $photo);
  }

  private function parseFilters($filterOpts)
  {
    $groupsObj = new Group;
    // If the user is logged in then we can display photos based on group membership
    $permission = 0;
    if($this->user->isOwner())
    {
      $permission = 1;
    }
    elseif($this->user->isLoggedIn())
    {
      $userGroups = $groupsObj->getGroups($this->user->getEmailAddress());
      if(!empty($userGroups))
      {
        $permission = -1;
        $groupIds = array();
        foreach($userGroups as $group)
          $groupIds[] = $group['id'];
      }
    }

    // This section enables in path parameters which are normally GET
    $pageSize = getConfig()->get('pagination')->pageSize;
    $filters = array('sortBy' => 'dateTaken,desc');
    if($filterOpts !== null)
    {
      $filterOpts = (array)explode('/', $filterOpts);
      foreach($filterOpts as $value)
      {
        $dashPosition = strpos($value, '-');
        if(!$dashPosition)
          continue;

        $parameterKey = substr($value, 0, $dashPosition);
        $parameterValue = substr($value, ($dashPosition+1));
        switch($parameterKey)
        {
          case 'pageSize':
            $pageSize = intval($parameterValue);
            break;
          case 'sortBy':
            $sortOptions = (array)explode(',', $value);
            if(count($sortOptions) != 2 || preg_match('/[^a-zA-Z0-9,]/', $parameterValue))
              continue;
            $filters[$parameterKey] = $parameterValue;
            break;
          default:
            $filters[$parameterKey] = $parameterValue;
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
    $protocol = $this->utility->getProtocol(false);
    if(isset($filters['protocol']))
      $protocol = $filters['protocol'];

    if($permission == 0)
      $filters['permission'] = $permission;
    elseif($permission == -1)
      $filters['groups'] = $groupIds;

    return array('filters' => $filters, 'pageSize' => $pageSize, 'protocol' => $protocol, 'page' => $page);
  }

  /**
   * Remove all the size keys from the photo but the one in list in $sizes
   *
   * @param $photo the photo object to prune.
   * @param array $sizes the sizes to keep.
   * @return the photo
   */
  private function pruneSizes($photo, $sizes)
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
        if(isset($sizekeys) && isset($sizekeys[$photoKey]))
          continue;
        unset($photo[$photoKey]);
      }
    }
    return $photo;
  }
}
