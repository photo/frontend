<?php
/**
 * FacebookConnect
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class PostUploadSharePlugin extends PluginBase
{
  public function __construct()
  {
    parent::__construct();
  }

  public function defineConf()
  {
    return array('sendgrid_user' => 'openphoto', 'sendgrid_password' => 'tfoehsje');
  }

  public function defineRoutes()
  {
    return array(
      'email-send' => array('POST', '/email-send')
    );
  }

  public function renderPhotoUploaded()
  {
    $shareUrl = sprintf('http://%s/photos/sortBy-dateUploaded,desc/list', $_SERVER['HTTP_HOST']);
    $emailPostUrl = $this->getRouteUrl('email-send');
    return <<<MKP
<div class="upload-share-facebook span6 offset2">
  <h3>
    <div class="fb-send" data-href="{$shareUrl}"></div> to your friends on Facebook
    <form class="form-stacked">
      <div><label>Click the send button above to start.</label></div>
    </form>
  </h3>
</div>
<div class="upload-share-email span6">
  <h3>Share by Email</h3>
  <form class="form-stacked" method="post" action="{$emailPostUrl}">
    <div class="clearfix">
      <label for="share-email-addresses">Email addresses</label>
      <div class="input">
        <textarea class="xlarge" id="share-email-addresses" name="share-email-addresses" rows="2"></textarea>
      </div>
    </div>
    <div class="clearfix">
      <label for="share-email-subject">Subject</label>
      <div class="input">
        <input type="text" class="xlarge" id="share-email-subject" name="share-email-subject" value="I uploaded some photos to share">
      </div>
    </div>
    <div class="clearfix">
      <label for="share-email-message">Message</label>
      <div class="input">
        <textarea class="xlarge" id="share-email-message" name="share-email-message" rows="4">I've uploaded some photos I'd like you to see. Click the link below to view them.

{THUMBNAILS}

{$shareUrl}</textarea>
      </div>
    </div>
    <div class="actions">
      <input type="submit" class="btn primary" value="Send email">
    </div>
  </form>
</div>
MKP;
  }

  public function routeHandler($route)
  {
    parent::routeHandler($route);
    switch($route)
    {
      case '/email-send':
        $this->emailSend();
        break;
    }
  }

  private function emailSend()
  {
    $conf = $this->getConf();
    $recipients = (array)explode(',', str_replace("\n", ',', $_POST['share-email-addresses']));

    $message = $_POST['share-email-message'];
    if(strstr($message, '{THUMBNAILS}') !== false)
    {
      $timeLimit = time() - 86400;
      $thumbnails = '';
      $resp = $this->api->invoke('/photos/list.json', EpiRoute::httpGet, array('_GET' => array('sortBy' => 'dateUploaded,desc', 'pageSize' => '5', 'returnSizes' => '150x150', 'generate' => 'true')));
      if($resp['code'] === 200 && $resp['result'][0]['totalRows'] > 0)
      {
        $photos = $resp['result'];
        foreach($photos as $photo)
        {
          if($photo['dateUploaded'] > $timeLimit)
            $includePhotos[] = $photo;
          $thumbnails .= sprintf('<a href="%s"><img src="%s" hspace="5" vspace="5"></a>', $photo['url'], $photo['path150x150']);
        }
      }

      $message = str_replace('{THUMBNAILS}', $thumbnails, $message);
    }

    $ch = curl_init('https://sendgrid.com/api/mail.send.json');
    $params = array();
    $params['to'] = $recipients;
    $params['subject'] = $_POST['share-email-subject'];
    $params['html'] = nl2br($message);
    $params['from'] = $this->config->user->email;
    $params['replyto'] = $this->config->user->email;
    $params['date'] = date('r');
    $params['headers'] = json_encode(array('X-Mailer' => 'Trovebox'));
    $params['api_user'] = $conf->sendgrid_user;
    $params['api_key'] = $conf->sendgrid_password;
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_exec($ch);
    curl_close($ch);

    $url = new Url;
    $this->route->redirect($url->photosView('sortBy-dateUploaded,desc', false));
  }
}
