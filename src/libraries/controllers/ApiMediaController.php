<?php
/**
 * Media controller for API endpoints
 *
 * @author James Walker <walkah@walkah.net>
 */
class ApiMediaController extends ApiBaseController
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Upload new media.
   *
   * @return string standard json envelope
   */
  public function upload()
  {
    $httpObj = new Http;
    $attributes = $_REQUEST;

    $albums = array();
    if(isset($attributes['albums']) && !empty($attributes['albums']))
      $albums = (array)explode(',', $attributes['albums']);
    $token = null;
    if(isset($attributes['token']) && !empty($attributes['token']))
    {
      $shareTokenObj = new ShareToken;
      $tokenArr = $shareTokenObj->get($attributes['token']);
      if(empty($tokenArr) || $tokenArr['type'] != 'upload')
        return $this->forbidden('No permissions with the passed in token', false);
      $attributes['albums'] = $tokenArr['data'];
      $token = $tokenArr['id'];
      $attributes['permission'] = '0';
    }
    else
    {
      getAuthentication()->requireAuthentication(array(Permission::create), $albums);
      getAuthentication()->requireCrumb();
    }

    // determine localFile
    extract($this->parseMediaFromRequest());
    
    // Get file mimetype by instantiating a photo object
    //  getMediaType is defined in parent abstract class Media
    $photoObj = new Photo;
    $mediaType = $photoObj->getMediaType($localFile);

    // Invoke type-specific
    switch ($mediaType) {
      case Media::typePhoto:
        return $this->api->invoke("/{$this->apiVersion}/photo/upload.json", EpiRoute::httpPost);
      case Media::typeVideo:
        return $this->api->invoke("/{$this->apiVersion}/video/upload.json", EpiRoute::httpPost);
    }
    
    return $this->error('Unsupported media type', false);
  }

  /**
   *
   */
  private function parseMediaFromRequest()
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
        // override the local filename so we know how to manage it
        // see gh-1465 for details
        $localFile = tempnam($this->config->paths->temp, 'opme-via-url');
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
