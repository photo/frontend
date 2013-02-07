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
    $status = $this->photo->delete($id);
    if($status)
    {
      $activityObj = new Activity;
      $activityObj->deleteForElement($id, array('photo-upload','photo-update','action-create'));

      $res = $this->api->invoke("/{$this->apiVersion}/photo/{$id}/view.json");
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
      $response = $this->api->invoke("/{$this->apiVersion}/photo/{$id}/delete.json", EpiRoute::httpPost, array('_POST' => $params));
      $retval = $retval && $response['result'] !== false;
    }

    if($retval)
      return $this->noContent(sprintf('%d photos deleted', count($ids)), true);
    else
      return $this->error('Error deleting one or more photos', false);
  }

  /**
    * Delete the source files for a photo specified by the ID.
    *
    * @param string $id ID of the photo to be deleted.
    * @return string Standard JSON envelope
    */
  public function deleteSource($id)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $status = $this->photo->deleteSourceFiles($id);
    if($status)
    {
      return $this->success('Photo source files deleted successfully', true);
    }
    else
    {
      return $this->error('Photo source file deletion failure', false);
    }
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
    $photoResp = $this->api->invoke("/{$this->apiVersion}/photo/{$id}/view.json", EpiRoute::httpGet);
    $groupsResp = $this->api->invoke("/{$this->apiVersion}/groups/list.json", EpiRoute::httpGet);
    $albumsResp = $this->api->invoke("/{$this->apiVersion}/albums/list.json", EpiRoute::httpGet, array('_GET' => array('pageSize' => 0)));
    $photo = $photoResp['result'];
    $groups = $groupsResp['result'];
    $albums = $albumsResp['result'];
    if(!$groups)
      $groups = array();
    if(!$albums)
      $albums = array();
    if($photo)
    {
      $template = sprintf('%s/photo-edit.php', $this->config->paths->templates);
      $license = null;
      if(isset($photo['license']))
        $license = $photo['license'];
      $this->template->url = new Url;
      $this->template->utility = new Utility;
      $markup = $this->template->get($template, array('photo' => $photo, 'albums' => $albums, 'groups' => $groups, 'licenses' => $this->utility->getLicenses($license), 'crumb' => getSession()->get('crumb')));
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
    $db = getDb();
    extract($this->parseFilters($filterOpts));
    $nextPrevious = $db->getPhotoNextPrevious($id, $filters);
    if(!$nextPrevious)
      return $this->error('Could not get next/previous photo', false);

    $sizes = array();
    if(isset($_GET['returnSizes']))
      $sizes = array_unique((array)explode(',', $_GET['returnSizes']));

    foreach($nextPrevious as $topKey => $photos)
    {
      foreach($photos as $innerKey => $photo)
        $nextPrevious[$topKey][$innerKey] = $this->pruneSizes($photo, $sizes);
    }

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
        foreach($nextPrevious as $topKey => $photos)
        {
          foreach($photos as $innerKey => $photo)
          {
            $options = $this->photo->generateFragmentReverse($size);
            if($generate && !isset($nextPrevious[$topKey][$innerKey]["path{$size}"]))
            {
              $hash = $this->photo->generateHash($photo['id'], $options['width'], $options['height'], $options['options']);
              $this->photo->generate($photo['id'], $hash, $options['width'], $options['width'], $options['options']);
              $requery = true;
            }
          }
        }
      }

      // requery to get generated paths
      if($requery)
      {
        $nextPrevious = $db->getPhotoNextPrevious($id, $filters);
        foreach($nextPrevious as $topKey => $photos)
        {
          foreach($photos as $innerKey => $photo)
            $nextPrevious[$topKey][$innerKey] = $this->pruneSizes($photo, $sizes);
        }
      }
    }

    foreach($nextPrevious as $topKey => $photos)
    {
      foreach($photos as $innerKey => $photo)
      {
        $nextPrevious[$topKey][$innerKey] = $this->photo->addApiUrls($photo, $sizes);
        if(!$this->user->isAdmin() && $this->config->site->decreaseLocationPrecision === '1')
        {
          if(isset($nextPrevious[$topKey][$innerKey]['latitude']))
            $nextPrevious[$topKey][$innerKey]['latitude'] = $this->utility->decreaseGeolocationPrecision($nextPrevious[$topKey][$innerKey]['latitude']);
          if(isset($nextPrevious[$topKey][$innerKey]['longitude']))
            $nextPrevious[$topKey][$innerKey]['longitude'] = $this->utility->decreaseGeolocationPrecision($nextPrevious[$topKey][$innerKey]['longitude']);
        }
      }
    }

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
      return $this->success('Your search did not return any photos', $photos);

    $sizes = array();
    if(isset($filters['returnSizes']))
      $sizes = array_unique((array)explode(',', $filters['returnSizes']));

    $generate = $requery = false;
    if(isset($_GET['generate']) && $_GET['generate'] == 'true')
      $generate = true;

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
    {
      $photos[$key] = $this->photo->addApiUrls($photos[$key], $sizes);
      if(!$this->user->isAdmin() && $this->config->site->decreaseLocationPrecision === '1')
      {
        if(isset($photos[$key]['latitude']))
          $photos[$key]['latitude'] = $this->utility->decreaseGeolocationPrecision($photos[$key]['latitude']);
        if(isset($photos[$key]['longitude']))
          $photos[$key]['longitude'] = $this->utility->decreaseGeolocationPrecision($photos[$key]['longitude']);
      }
    }

    if(!empty($photos))
    {
      $photos[0]['currentPage'] = intval($page);
      $photos[0]['currentRows'] = count($photos);
      $photos[0]['pageSize'] = intval($pageSize);
      $photos[0]['totalPages'] = !empty($pageSize) ? ceil($photos[0]['totalRows'] / $pageSize) : 0;
    }

    return $this->success("Successfully retrieved user's photos", $photos);
  }

  /**
    * Replace the binary image file and the associated hash
    * This method does not take any additional parameters
    *   call the update API to update meta data
    *
    * @param string $id ID of the photo to be updated.
    * @return string Standard JSON envelope
    */
  public function replace($id)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();

    $attributes = $_REQUEST;

    // this determines where to get the photo from and populates $localFile and $name
    extract($this->parsePhotoFromRequest());

    $hash = sha1_file($localFile);
    $allowDuplicate = $this->config->site->allowDuplicate;
    if(isset($attributes['allowDuplicate']))
      $allowDuplicate = $attributes['allowDuplicate'];
    if($allowDuplicate == '0')
    {
      $hashResp = $this->api->invoke("/{$this->apiVersion}/photos/list.json", EpiRoute::httpGet, array('_GET' => array('hash' => $hash)));
      if($hashResp['result'][0]['totalRows'] > 0)
        return $this->conflict('This photo already exists based on a sha1 hash. To allow duplicates pass in allowDuplicate=1', false);
    }

    // auto rotation is enabled by default but requires exiftran
    if(!isset($attributes['allowAutoRotate']) || $attributes['allowAutoRotate'] != '0')
    {
      $exiftran = $this->config->modules->exiftran;
      if(is_executable($exiftran))
        exec(sprintf('%s -ai %s', $exiftran, escapeshellarg($localFile)));
    }

    // TODO put this in a whitelist function (see upload())
    if(isset($attributes['__route__']))
      unset($attributes['__route__']);
    if(isset($attributes['photo']))
      unset($attributes['photo']);
    if(isset($attributes['crumb']))
      unset($attributes['crumb']);
    if(isset($attributes['returnSizes']))
    {
      $returnSizes = implode(',', array_unique((array)explode(',', $attributes['returnSizes'])));
      unset($attributes['returnSizes']);
    }

    $tagObj = new Tag;
    if(isset($attributes['tags']) && !empty($attributes['tags']))
      $tagObj->createBatch($attributes['tags']);

    $status = $this->photo->replace($id, $localFile, $name, $attributes);
    if(!$status)
      return $this->error(sprintf('Could not complete the replacement of photo %s', $id), false);

    $photoResp = $this->api->invoke("/{$this->apiVersion}/photo/{$id}/view.json", EpiRoute::httpGet);
    return $this->success(sprintf('Photo %s was successfully replaced.', $id), $photoResp['result']);
  }

  public function share($ids)
  {
    $idsArr = (array)explode(',', $ids);
    $photoResp = $this->api->invoke(sprintf('/photo/%s/view.json', $idsArr[0]), EpiRoute::httpGet, array('_GET' => array('returnSizes' => '200x200')));
    if($photoResp['code'] !== 200)
      return $this->error('Could not get first photo to share.', false);

    $markup = $this->theme->get('partials/share.php', array('photo' => $photoResp['result'], 'ids' => $idsArr, 'crumb' => $this->session->get('crumb')));
    return $this->success('Photo share form', array('markup' => $markup));
  }

  public function sharePost($ids)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $email = $this->session->get('email');
    if(empty($email) || empty($_POST['message']) || empty($_POST['recipients']))
      return $this->error('Not all parameters were passed in', false);

    $photoResp = $this->api->invoke(sprintf('/photo/%s/view.json', $ids), EpiRoute::httpGet);
    $photo = $photoResp['result'];
    if($photoResp['code'] !== 200)
      return $this->error('Could retrieve photo data', false);

    $emailer = new Emailer($email);
    $emailer->setRecipients(array_merge(array($email), (array)explode(',', $_POST['recipients'])));

    $body = nl2br($_POST['message']);
    if(!isset($_POST['attachment']))
    {
      $body .= sprintf('<p><img src="%s"></p>', $photo['pathBase']);
    }
    else
    {
      $localFile = $this->photo->storeLocally($photo['pathBase']);
      if($localFile === false)
        return $this->error('Could not complete request', false);

      $emailer->addAttachment($localFile, $photo['filenameOriginal']);
    }

    $subject = sprintf('%s has been shared with you', $photo['filenameOriginal']);
    if(!empty($photo['title']))
      $subject = $photo['title'];
    $emailer->setSubject($subject);
    $emailer->setBody(strip_tags($body), $body);
    $emailer->send();

    if(isset($localFile) && $localFile !== false)
      unlink($localFile);

    return $this->success('yes', array('ids' => $ids, 'post' => $_POST));  
  }

  /**
    * Transform a photo.
    * Modifies a photo by rotating/BW/etc.
    *
    * @param $id string ID of the photo to transform
    * @return string standard json envelope
    */
  public function transform($id)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $res = $this->photo->transform($id, $_POST);

    if($res) 
    {
      $apiResp = $this->api->invoke("/{$this->apiVersion}/photo/{$id}/view.json", EpiRoute::httpGet);
      $photo = $apiResp['result'];
      return $this->success('Successfully transformed the photo', $photo);
    }
    else
    {
      return $this->error('Could not transform the photo', false);
    }
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
    $tagObj = new Tag;
    $attributes = $_REQUEST;

    $this->plugin->invoke('onPhotoUpload');

    // this determines where to get the photo from and populates $localFile and $name
    extract($this->parsePhotoFromRequest());

    // check if file type is valid
    $utility = new Utility;
    if(!$utility->isValidMimeType($localFile))
    {
      $this->logger->warn(sprintf('Invalid mime type for %s', $localFile));
      unlink($localFile);
      return $this->error('Invalid mime type', false);;
    }

    // TODO put this in a whitelist function (see replace())
    if(isset($attributes['__route__']))
      unset($attributes['__route__']);
    if(isset($attributes['photo']))
      unset($attributes['photo']);
    if(isset($attributes['crumb']))
      unset($attributes['crumb']);
    if(isset($attributes['returnSizes']))
    {
      $returnSizes = implode(',', array_unique((array)explode(',', $attributes['returnSizes'])));
      unset($attributes['returnSizes']);
    }

    $photoId = false;

    $attributes['hash'] = sha1_file($localFile);
    // set default to config and override with parameter
    $allowDuplicate = $this->config->site->allowDuplicate;
    if(isset($attributes['allowDuplicate']))
      $allowDuplicate = $attributes['allowDuplicate'];
    if($allowDuplicate == '0')
    {
      $hashResp = $this->api->invoke("/{$this->apiVersion}/photos/list.json", EpiRoute::httpGet, array('_GET' => array('hash' => $attributes['hash'])));
      // the second condition is for backwards compatability between v2 and v1. See #1086
      if(!empty($hashResp['result']) && $hashResp['result'][0]['totalRows'] > 0)
      {
        unlink($localFile);
        return $this->conflict('This photo already exists based on a sha1 hash. To allow duplicates pass in allowDuplicate=1', $hashResp['result'][0]);
      }
    }

    // auto rotation is enabled by default but requires exiftran
    if(!isset($attributes['allowAutoRotate']) || $attributes['allowAutoRotate'] != '0')
    {
      $exiftran = $this->config->modules->exiftran;
      if(is_executable($exiftran))
        exec(sprintf('%s -ai %s', $exiftran, escapeshellarg($localFile)));
    }

    if(isset($attributes['tags']) && !empty($attributes['tags']))
      $tagObj->createBatch($attributes['tags']);

    $photoId = $this->photo->upload($localFile, $name, $attributes);

    if($photoId)
    {
      if(isset($attributes['albums']))
        $this->updateAlbums($attributes['albums'], $photoId);

      // jmathai - check where this gets set
      if(isset($returnSizes))
      {
        $sizes = (array)explode(',', $returnSizes);
        if(!in_array('100x100xCR', $sizes))
          $sizes[] = '100x100xCR';
      }
      else
      {
        $sizes = array('100x100xCR');
      }

      foreach($sizes as $size)
      {
        $options = $this->photo->generateFragmentReverse($size);
        $hash = $this->photo->generateHash($photoId, $options['width'], $options['height'], $options['options']);
        $this->photo->generate($photoId, $hash, $options['width'], $options['height'], $options['options']);
      }

      $apiResp = $this->api->invoke("/{$this->apiVersion}/photo/{$photoId}/view.json", EpiRoute::httpGet, array('_GET' => array('returnSizes' => implode(',', $sizes))));
      $photo = $apiResp['result'];

      $webhookApi = $this->api->invoke("/{$this->apiVersion}/webhooks/photo.upload/list.json", EpiRoute::httpGet);
      if(!empty($webhookApi['result']) && is_array($webhookApi['result']))
      {
        $photoAsArgs = $photo;
        $photoAsArgs['tags'] = implode(',', $photoAsArgs['tags']);
        foreach($webhookApi['result'] as $key => $hook)
        {
          $httpObj->fireAndForget($hook['callback'], 'POST', $photoAsArgs);
          $this->logger->info(sprintf('Webhook callback executing for photo.upload: %s', $hook['callback']));
        }
      }
      $this->plugin->setData('photo', $photo);
      $this->plugin->invoke('onPhotoUploaded');

      $permission = isset($attributes['permission']) ? $attributes['permission'] : 0;
      $this->api->invoke(
        "/{$this->apiVersion}/activity/create.json", 
        EpiRoute::httpPost, 
        array('_POST' => array('elementId' => $photo['id'], 'type' => 'photo-upload', 'data' => $photo, 'permission' => $permission))
      );
      $this->user->setAttribute('stickyPermission', $permission);
      $this->user->setAttribute('stickyLicense', $photo['license']);
      return $this->created("Photo {$photoId} uploaded successfully", $photo);
    }

    return $this->error('File upload failure', false);
  }

  /**
    * Display a confirmation page for the upload
    *
    * @return string Standard JSON envelope
    */
  public function uploadConfirm()
  {
    $params = $_POST;
    $params['successIds'] = $params['duplicateIds'] = array();
    if(isset($params['success']) && !empty($params['success']))
    {
      foreach($params['success'] as $p)
        $params['successIds'][] = $p['id'];
    }
    if(isset($params['duplicate']) && !empty($params['duplicate']))
    {
      foreach($params['duplicate'] as $p)
        $params['duplicateIds'][] = $params['successIds'][] = $p['id'];
    }
    if(!isset($params['failure']))
      $params['failure'] = array();

    $params['successIds'] = implode(',', $params['successIds']);

    $returnSizes = $this->config->photoSizes->thumbnail;

    $params['successPhotos'] = array();
    $params['duplicateCount'] = count($params['duplicateIds']);
    unset($params['duplicateIds']);
    if(count($params['successIds']) > 0)
    {
      $photosResp = $this->api->invoke('/photos/list.json', EpiRoute::httpGet, array('_GET' => array('pageSize' => '0', 'ids' => $params['successIds'], 'returnSizes' => $returnSizes)));
      if($photosResp['code'] === 200 && $photosResp['result'][0]['totalRows'] > 0)
        $params['successPhotos'] = $photosResp['result'];
    }

    $params['facebookId'] = false;
    if($this->plugin->isActive('FacebookConnectHosted'))
    {
      $fbConf = $this->plugin->loadConf('FacebookConnectHosted');
      $params['facebookId'] = $fbConf['id'];
    }

    if(count($params['ids']) > 0)
    {
      $ids = implode(',', $params['ids']);
      $params['url'] = $this->url->photosView("ids-{$ids}", false);
      $resourceMapResp = $this->api->invoke('/s/create.json', EpiRoute::httpPost, array('_POST' => array('uri' => $params['url'], 'method' => 'GET', 'crumb' => $this->session->get('crumb'))));
      if($resourceMapResp['code'] === 201)
        $params['url'] = $this->url->resourceMap($resourceMapResp['result']['id'], false);
    }

    $template = sprintf('%s/uploadConfirm.php', $this->config->paths->templates);
    $body = $this->template->get($template, $params);
    return $this->success('Photos uploaded successfully', array('tpl' => $body, 'data' => $params));
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
    $this->logger->info(sprintf('Calling ApiPhotoController::update with %s', $id));
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    if(isset($params['crumb']))
      unset($params['crumb']);

    $activityObj = new Activity;
    $albumObj = new Album;
    $tagObj = new Tag;

    $params = $_POST;
    $photoBefore = $this->api->invoke("/{$this->apiVersion}/photo/{$id}/view.json", EpiRoute::httpGet);
    $photoBefore = $photoBefore['result'];

    if(isset($params['tagsAdd']))
      $params['tags'] = implode(',', array_unique(array_merge($photoBefore['tags'], (array)explode(',', $params['tagsAdd']))));

    // since tags can be created adhoc we need to ensure they're here
    if(isset($params['tags']) && !empty($params['tags']))
      $tagObj->createBatch($params['tags']);

    // if the permission of a photo changes we have to clean some stuff up
    if(isset($params['permission']) && $params['permission'] != $photoBefore['permission'])
    {
      // if a public photo is marked private we delete related activity
      if($params['permission'] == 0)
        $activityObj->deleteForElement($id, array('photo-upload','photo-update','action-create'));

      // if no albums or tags are passed we have to manually adjust the counters
      // the triggers only adjust them if we update the albums on this photo
      $albumObj->adjustCounters($this->photo->getAlbumsForPhoto($id), $params['permission']);

      if(!isset($params['tags']) && !empty($photoBefore['tags']))
        $tagObj->adjustCounters($photoBefore['tags'], $params['permission']);
    }

    $photoUpdatedId = $this->photo->update($id, $params);

    if($photoUpdatedId)
    {
      $apiResp = $this->api->invoke("/{$this->apiVersion}/photo/{$id}/view.json", EpiRoute::httpGet, array('_GET' => array('returnSizes' => '100x100xCR', 'generate' => 'true')));
      $photo = $apiResp['result'];

      $post = array('elementId' => $photoUpdatedId, 'type' => 'photo-update', 'data' => $photo, 'permission' => isset($params['permission']) ? $params['permission'] : 0);
      $this->api->invoke("/{$this->apiVersion}/activity/create.json", EpiRoute::httpPost, array('_POST' => $post));

      return $this->success("photo {$id} updated", $photo);
    }

    return $this->error("photo {$id} could not be updated", false);
  }

  /**
    * Update the data associated with the photo in the remote data store.
    * Parameters to be updated are in _POST
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
      $response = $this->api->invoke("/{$this->apiVersion}/photo/{$id}/update.json", EpiRoute::httpPost, array('_POST' => $params));
      $retval = $retval && $response['result'] !== false;
    }

    if($retval)
      return $this->success(sprintf('%d photos updated', count($ids)), true);
    else
      return $this->error('Error updating one or more photos', false);
  }

  /**
    * Form for batch editing
    *
    * @return string Standard JSON envelope
    */
  public function updateBatchForm()
  {
    getAuthentication()->requireAuthentication();
    $params = $_GET;
    if($params['action'] == 'albumsAdd')
    {
      $albumsResp = $this->api->invoke('/albums/list.json', EpiRoute::httpGet, array('_GET' => array('pageSize' => 0)));
      if($albumsResp['code'] === 200)
        $params['albums'] = $albumsResp['result'];
    }
    $markup = $this->theme->get('partials/batch-update-form.php', $params);
    return $this->success('Batch update form', array('markup' => $markup));
  }

  /**
    * Retrieve a photo from the remote datasource.
    *
    * @param string $id ID of the photo to be viewed.
    * @param string $options Optional options for rendering this photo.
    * @return string Standard JSON envelope
    */
  public function view($id, $options = null)
  {
    $db = getDb();
    $getActions = isset($_GET['actions']) && $_GET['actions'] == 'true';
    if($getActions)
      $photo = $db->getPhotoWithActions($id);
    else
      $photo = $db->getPhoto($id);

    // check permissions
    if(!isset($photo['id']))
    {
      return $this->notFound("Photo {$id} not found", false);
    }
    elseif(!$this->user->isAdmin())
    {
      if($photo['permission'] == 0)
      {
        if(!$this->user->isLoggedIn() || (isset($photo['groups']) && empty($photo['groups'])))
          return $this->notFound("Photo {$id} not found", false);

        // can't call API since we're not the owner
        $userGroups = $db->getGroups($this->user->getEmailAddress());
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
      $sizes = array_unique((array)explode(',', $_GET['returnSizes']));
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
        $fragment = $this->photo->generateFragmentReverse($size);
        if($generate && !isset($photo["path{$size}"]))
        {
          $hash = $this->photo->generateHash($id, $fragment['width'], $fragment['height'], $fragment['options']);
          $this->photo->generate($id, $hash, $fragment['width'], $fragment['width'], $fragment['options']);
          $requery = true;
        }
      }

      // requery to get generated paths
      if($requery)
      {
        if($getActions)
          $photo = $db->getPhotoWithActions($id);
        else
          $photo = $db->getPhoto($id);

        $photo = $this->pruneSizes($photo, $sizes);
      }
    }

    if(isset($_GET['nextprevious']) && !empty($_GET['nextprevious']))
    {
      if(empty($options))
        $apiNextPrevious = $this->api->invoke("/photo/{$id}/nextprevious.json", EpiRoute::httpGet);
      else
        $apiNextPrevious = $this->api->invoke("/photo/{$id}/nextprevious/{$options}.json", EpiRoute::httpGet);

      if($apiNextPrevious['code'] === 200)
      {
        $photo['next'] = $apiNextPrevious['result']['next'];
        $photo['previous'] = $apiNextPrevious['result']['previous'];
      }
    }

    $photo = $this->photo->addApiUrls($photo, $sizes);
    if(!$this->user->isAdmin() && $this->config->site->decreaseLocationPrecision === '1')
    {
      if(isset($photo['latitude']))
        $photo['latitude'] = $this->utility->decreaseGeolocationPrecision($photo['latitude']);
      if(isset($photo['longitude']))
        $photo['longitude'] = $this->utility->decreaseGeolocationPrecision($photo['longitude']);
    }
    return $this->success("Photo {$id}", $photo);
  }

  // To do multiple photoIds we have to have corresponding photoBefores and map them accordingly
  private function updateAlbums($albumIds, $photoId, $photoBefore = array())
  {
    $albumsArr = $albumIds;
    if(!is_array($albumIds))
      $albumsArr = (array)explode(',', $albumIds);

    if(!isset($photoBefore['albums']))
      $photoBefore['albums'] = array();
    $albumsToRemove = array_diff($photoBefore['albums'], $albumsArr);
    $albumsToAdd = array_diff($albumsArr, $photoBefore['albums']);
    if(!empty($albumsToRemove))
    {
      foreach($albumsToRemove as $aId)
      {
        if(!empty($aId))
          $this->api->invoke("/album/{$aId}/photo/remove.json", EpiRoute::httpPost, array('_POST' => array('ids' => $photoId)));
      }
    }
    if(!empty($albumsToAdd))
    {
      foreach($albumsToAdd as $aId)
      {
        if(!empty($aId))
          $this->api->invoke("/album/{$aId}/photo/add.json", EpiRoute::httpPost, array('_POST' => array('ids' => $photoId)));
      }
    }
  }

  // To do multiple photoIds we have to have corresponding photoBefores and map them accordingly
  private function updateTags($tagIds, $photoId, $photoBefore = array())
  {
    $tagObj = new Tag;
    $tagsArr = $tagIds;
    if(!is_array($tagIds))
      $tagsArr = (array)explode(',', $tagIds);

    if(!isset($photoBefore['tags']))
      $photoBefore['tags'] = array();
    $tagsToRemove = array_diff($photoBefore['tags'], $tagsArr);
    $tagsToAdd = array_diff($tagsArr, $photoBefore['tags']);
    if(!empty($tagsToRemove))
    {
      foreach($tagsToRemove as $tId)
      {
        if(!empty($tId))
          $tag->removeTagFromPhoto($tId, $photoId);
      }
    }
    if(!empty($tagsToAdd))
    {
      foreach($tagsToAdd as $tId)
      {
        if(!empty($tId))
          $tag->addTagToPhoto($tId, $photoId);
      }
    }
  }

  protected function parseFilters($filterOpts)
  {
    // If the user is logged in then we can display photos based on group membership
    $shareTokenObj = new ShareToken;

    $token = null;
    $permission = 0;
    if($this->user->isAdmin())
      $permission = 1;

    // This section enables in path parameters which are normally GET
    $pageSize = $this->config->pagination->photos;
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
          case 'token':
            $token = $shareTokenObj->get($parameterValue);
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

    if($token !== null)
    {
      if($token !== false)
      {
        switch($token['type'])
        {
          case 'album':
            if(isset($filters['album']) && $filters['album'] == $token['data'])
              $permission = 1; // set permission to be pubilc for this request
        }
      }
    }

    if($permission == 0)
      $filters['permission'] = $permission;

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

    // adjust height/width values based on rotation see #484
    if($photo['rotation'] == '90' || $photo['rotation'] == '270')
      list($photo['width'], $photo['height']) = array($photo['height'], $photo['width']);
    return $photo;
  }

  private function parsePhotoFromRequest()
  {
    $name = '';
    if(isset($_FILES) && isset($_FILES['photo']))
    {
      $localFile = $_FILES['photo']['tmp_name'];
      $name = $_FILES['photo']['name'];
    }
    elseif(isset($_POST['photo']))
    {
      // if a filename is passed in we use it else it's the random temp name
      $localFile = tempnam($this->config->paths->temp, 'opme');
      $name = basename($localFile).'.jpg';

      // if we have a path to a photo we download it
      // else we base64_decode it
      if(preg_match('#https?://#', $_POST['photo']))
      {
        $fp = fopen($localFile, 'w');
        $ch = curl_init($_POST['photo']);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        // TODO configurable timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
      }
      else
      {
        file_put_contents($localFile, base64_decode($_POST['photo']));
      }
    }

    if(isset($_POST['filename']))
      $name = $_POST['filename'];

    return array('localFile' => $localFile, 'name' => $name);

  }
}
