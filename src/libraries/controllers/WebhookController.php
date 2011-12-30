<?php
/**
  * Photo controller for HTML endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class WebhookController extends BaseController
{
  /**
    * Subscribe to a topic (creates a webhook).
    *
    * @return void
    */
  public function subscribe()
  {
    getAuthentication()->requireAuthentication();
    $params = $_POST;
    $params['verify'] = 'sync';
    if(isset($params['callback']) && isset($params['mode']) && isset($params['topic']))
    {
      $urlParts = parse_url($params['callback']);
      if(isset($urlParts['scheme']) && isset($urlParts['host']))
      {
        if(!isset($urlParts['port']))
          $port = '';
        if(!isset($urlParts['path']))
          $path = '';
        extract($urlParts);

        $challenge = uniqid();
        $queryParams = array();
        if(isset($urlParts['query']) && !empty($urlParts['query']))
          parse_str($urlParts['query'], $queryParams);
        $queryParams['mode'] = $params['mode'];
        $queryParams['topic'] = $params['topic'];
        $queryParams['challenge'] = $challenge;
        if(isset($params['verifyToken']))
          $queryParams['verifyToken'] = $params['verifyToken'];

        $queryString = '';
        if(!empty($queryParams))
          $queryString = sprintf('?%s', http_build_query($queryParams));

        $url = sprintf('%s://%s%s%s%s', $scheme, $host, $port, $path, $queryString);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $handle = getCurl()->addCurl($ch);
        // verify a 2xx response and that the body is equal to the challenge
        if($handle->code >= 200 && $handle->code < 300 && $handle->data == $challenge)
        {
          $apiWebhook = $this->api->invoke('/webhook/create.json', EpiRoute::httpPost, array('_POST' => $params));
          if($apiWebhook['code'] === 200)
          {
            header('HTTP/1.1 204 No Content');
            getLogger()->info(sprintf('Webhook successfully created: %s', json_encode($params)));
            return;
          }
        }
        $message = sprintf('The verification call failed to meet requirements. Code: %d, Response: %s, Expected: %s, URL: %s', $handle->code, $handle->data, $challenge, $url);
        getLogger()->warn($message);
      }
      else
      {
        $message = sprintf('Callback url was invalid: %s', $params['callback']);
        getLogger()->warn($message);
      }
    }
    else
    {
      $message = sprintf('Not all required parameters were passed in to webhook subscribe: %s', json_encode($params));
      getLogger()->warn($message);
    }

    header('HTTP/1.1 400 Bad Request');
    echo $message;
  }
}
