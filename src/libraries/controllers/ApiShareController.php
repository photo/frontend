<?php
class ApiShareController extends ApiController
{
  public function __construct()
  {
    parent::__construct();
  }

  public function view($type, $data)
  {
    $dataArr = (array)explode(',', $data);
    $params = array('type' => $type, 'data' => $dataArr, 'crumb' => $this->session->get('crumb'));

    $token = null;
    $tokenResp = $this->api->invoke(sprintf('/token/%s/%s/list.json', $type, $data), EpiRoute::httpGet);
    if($tokenResp['code'] === 200)
    {
      if(count($tokenResp['result']) > 0)
      {
        $token = $tokenResp['result'][0]['id'];
      }
      else
      {
        $tokenResp = $this->api->invoke(sprintf('/token/%s/%s/create.json', $type, $data), EpiRoute::httpPost);
        if($tokenResp['code'] === 201)
          $token = $tokenResp['result']['id'];
      }
    }

    if(empty($token))
      return $this->error('Could not generate token for share form', false);


    if($type === 'photo')
    {
      $photoResp = $this->api->invoke(sprintf('/photo/%s/view.json', $dataArr[0]), EpiRoute::httpGet, array('_GET' => array('returnSizes' => '200x200,800x800', 'generate' => 'true')));
      if($photoResp['code'] !== 200)
        return $this->error('Could not get first photo to share.', false);
      $params['photo'] = $photoResp['result']['path200x200'];
      $params['photoLarge'] = $photoResp['result']['path800x800'];
      $params['title'] = $photoResp['result']['title'] != '' ? $photoResp['result']['title'] : $photoResp['result']['filenameOriginal'];
      $params['url'] = sprintf('%s/token-%s', $photoResp['result']['url'], $token);
      $params['permission'] = $photoResp['result']['permission'];
    }
    else
    {
      $albumResp = $this->api->invoke(sprintf('/album/%s/view.json', $dataArr[0]), EpiRoute::httpGet);
      if($albumResp['code'] !== 200)
        return $this->error('Could not get album to share.', false);

      $params['photo'] = $albumResp['result']['cover']['path200x200xCR'];
      $params['title'] = $albumResp['result']['name'];
      $utilityObj = new Utility;
      $path = sprintf('/photos/album-%s/token-%s/list', $data, $token);
      $params['url'] = $utilityObj->getAbsoluteUrl($path, false);
      $params['permission'] = 0;
    }

    $markup = $this->theme->get('partials/share.php', $params);
    return $this->success('Photo share form', array('markup' => $markup));
  }

  public function send($type, $data)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $email = $this->session->get('email');
    if(empty($email) || empty($_POST['message']) || empty($_POST['recipients']))
      return $this->error('Not all parameters were passed in', false);

    $emailer = new Emailer($email);
    $emailer->setRecipients(array_merge(array($email), (array)explode(',', $_POST['recipients'])));

    if($type === 'photo')
      $status = $this->sendPhotoEmail($data, $emailer);
    else
      $status = $this->sendAlbumEmail($data, $emailer);

    if(!$status)
      return $this->error('Could not complete request', false);

    return $this->success('yes', array('data' => $data, 'post' => $_POST)); 
  }

  private function sendAlbumEmail($data, $emailer)
  {
    $albumResp = $this->api->invoke(sprintf('/album/%s/view.json', $data), EpiRoute::httpGet);
    $album = $albumResp['result'];
    if($albumResp['code'] !== 200)
      return false;

    $body = nl2br($_POST['message']);
    $body .= sprintf('<p><img src="%s" style="padding:3px; border:solid 1px #ccc;"></p>', $album['cover']['path200x200xCR']);
    $body .= sprintf("\n<br><br>Click the link below to view the album.<br>\n<a href=\"%s\">%s</a>", $_POST['url'], $_POST['url']);

    $subject = sprintf('The album "%s" has been shared with you', $album['name']);
    $emailer->setSubject($subject);
    $emailer->setBody(strip_tags($body), $body);
    $emailer->send();

    return true;
  }

  private function sendPhotoEmail($data, $emailer)
  {
    $photoResp = $this->api->invoke(sprintf('/photo/%s/view.json', $data), EpiRoute::httpGet);
    $photo = $photoResp['result'];
    if($photoResp['code'] !== 200)
      return $this->error('Could retrieve photo data', false);

    $body = nl2br($_POST['message']);
    if(!isset($_POST['attachment']))
    {
      $body .= sprintf('<p><img src="%s"></p>', $photo['pathBase']);
    }
    else
    {
      $photoObj = new Photo;
      $localFile = $photoObj->storeLocally($photo['pathBase']);
      if($localFile === false)
        return false;

      $emailer->addAttachment($localFile, $photo['filenameOriginal']);
    }

    $subject = sprintf('The photo "%s" has been shared with you', $photo['filenameOriginal']);
    if(!empty($photo['title']))
      $subject = $photo['title'];
    $emailer->setSubject($subject);
    $emailer->setBody(strip_tags($body), $body);
    $emailer->send();

    if(isset($localFile) && $localFile !== false)
      unlink($localFile);

    return true;
  }
}
