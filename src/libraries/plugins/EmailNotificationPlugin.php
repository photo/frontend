<?php
/**
 * EmailNotificationPlugin is the parent class for every plugin.
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class EmailNotificationPlugin extends PluginBase
{
  public function __construct()
  {
    parent::__construct();
  }

  public function onAction()
  {
    parent::onAction();
    // TODO proper email sending
    $target = $this->plugin->getData('target');
    $actionResp = getApi()->invoke("/action/{$target['id']}/view.json", EpiRoute::httpGet);
    if($actionResp['code'] !== 200)
      return;

    $action = $actionResp['result'];
    $email = getConfig()->get('user')->email;
    $subject = 'You got a new comment on your photo';
    if($action['type'] == 'comment')
    {
      $body = <<<BODY
{$action['email']} left a comment on your photo.

====
{$action['value']}
====

See the comment here: {$action['permalink']}
BODY;
    }
    else
    {
      $body = <<<BODY
{$action['email']} favorited a photo of yours.

See the favorite here: {$action['permalink']}
BODY;
    }
    $headers = "From: Trovebox Robot <no-reply@trovebox.com>\r\n" .
        "Reply-To: no-reply@trovebox.com\r\n" .
        'X-Mailer: Trovebox';
    mail($email, $subject, $body, $headers);
  }
}
