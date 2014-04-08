<?php
/**
 * Video controller for API endpoints
 *
 * @author James Walker <walkah@walkah.net>
 */
class ApiVideoController extends ApiBaseController
{

  public function __construct()
  {
    parent::__construct();
    $this->video = new Video;
    $this->tag = new Tag;
    $this->user = new User;
  }

  /**
   * Upload a video.
   *
   * @return string standard json envelope
   */
  public function upload()
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $httpObj = new Http;
    $attributes = $_REQUEST;

    $this->plugin->invoke('onVideoUpload');

    // this determines where to get the photo from and populates $localFile and $name
    extract($this->parseVideoFromRequest());

    // TODO put this in a whitelist function (see replace())
    if(isset($attributes['__route__']))
      unset($attributes['__route__']);
    if(isset($attributes['photo']))
      unset($attributes['photo']);
    if(isset($attributes['crumb']))
      unset($attributes['crumb']);

    $videoId = false;

    $attributes['video'] = true;
    $attributes['hash'] = sha1_file($localFile);
    $attributes['width'] = $this->config->photos->baseSize;
    $attributes['height'] = $this->config->photos->baseSize;
    
    $videoId = $this->video->upload($localFile, $name, $attributes);
    if ($videoId)
    {
      $apiResp = $this->api->invoke("/{$this->apiVersion}/photo/{$videoId}/view.json", EpiRoute::httpGet, array('_GET' => array()));
      $video = $apiResp['result'];
      $permission = isset($attributes['permission']) ? $attributes['permission'] : 0;

      // TODO webhooks and things
      if ($video) { }

      $this->plugin->setData('video', $video);
      $this->plugin->setData('videoId', $videoId);
      $this->plugin->invoke('onVideoUploaded');

      $this->user->setAttribute('stickyPermission', $permission);
      $this->user->setAttribute('stickyLicense', $video['license']);
      return $this->created("Video {$videoId} uploaded successfully", $video);
    }
        
    return $this->error("File upload failure", false);
  }


  private function parseVideoFromRequest()
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