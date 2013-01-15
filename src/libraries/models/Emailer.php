<?php
class Emailer extends BaseModel
{
  private $mailer, $message, $transport = null;
  public function __construct($from = null)
  {
    parent::__construct();

    if(empty($from) || stristr($from, '@') === false)
      $from = $this->config->emailer->from;

    $this->message = Swift_Message::newInstance();
    $this->message->setFrom($from);
    if(isset($this->config->emailer->host))
    {
      $this->transport = Swift_SmtpTransport::newInstance($this->config->emailer->host, $this->config->emailer->port);
      if(isset($this->config->emailer->username))
        $this->transport->setUsername($this->config->emailer->username);
      if(isset($this->config->emailer->password))
        $this->transport->setPassword($this->config->emailer->password);
    }
  }

  public function setRecipients($recipients = array())
  {
    foreach((array)$recipients as $recipient)
      $this->message->setTo($recipient);
  }

  public function addBcc($recipients = array())
  {
    foreach((array)$recipients as $recipient)
      $this->message->setBcc($recipient);
  }

  public function setSubject($subject)
  {
    $this->message->setSubject($subject);
  }

  public function setBody($text, $html = null)
  {
    $this->message->setBody($text);
    if(!empty($html))
      $this->message->addPart($html, 'text/html');
  }

  public function addAttachment($file, $name)
  {
    $this->message->attach(Swift_Attachment::fromPath($file)->setFileName($name));
  }

  public function send()
  {
    if($this->transport === null)
      return false;

    $this->mailer = Swift_Mailer::newInstance($this->transport);
    return $this->mailer->send($this->message);
  }
}
