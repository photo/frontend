<?php
/**
  * User controller for API endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ApiUserController extends ApiBaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
    $this->user = new User;
  }

  /**
    * Log a user in via BrowserID
    *
    * @return string Standard JSON envelope
    */
  public function login($provider = null)
  {
    $wasUserLoggedIn = $this->user->login($provider, $_POST);
    if($wasUserLoggedIn)
      return $this->success('User was logged in successfully', array('email' => getSession()->get('email')));
    else
      return $this->forbidden('User was not able to be logged in', false);
  }

  /**
    * Log a user out.
    *
    * @return string Standard JSON envelope
    */
  public function logout()
  {
    $this->user->logout();
    return $this->success('User was logged out successfully', true);
  }

  /**
    * Generate a password reset token and email a link to the user.
    *
    * @return string Standard JSON envelope
    */
  public function passwordRequest()
  {
    if(!isset($_POST['email']))
      return $this->error('No email address provided.', false);

    $email = $_POST['email'];
    if($email == $this->config->user->email)
    {
      $token = md5(rand(10000,100000));
      $tokenUrl = sprintf('%s://%s/manage/password/reset/%s', $this->utility->getProtocol(false), $_SERVER['HTTP_HOST'], $token);
      $this->user->update(array('passwordToken' => $token));
      $templateObj = getTemplate();
      $template = sprintf('%s/email/password-reset.php', $this->config->paths->templates);
      $body = $this->template->get($template, array('tokenUrl' => $tokenUrl));
      $emailer = new Emailer;
      $emailer->setRecipients(array($this->config->user->email));
      $emailer->setSubject('OpenPhoto password reset request');
      $emailer->setBody($body);
      $result = $emailer->send();
      if($result > 0)
      {
        return $this->success('An email was sent to reset the password.', true);
      }
      else
      {
        $this->logger->info('Unable to send email. Confirm that your email settings are correct and the email addresses are valid.');
        return $this->error('We were unable to send a password reset email.', false);
      }
    }
    return $this->error('The email address provided does not match the registered email for this site.', false);
  }

  /**
    * Resets a user's password after validating the password token
    *
    * @return string Standard JSON envelope
    */
  public function passwordReset()
  {
    $user = new User;
    $token = $_POST['token'];
    $password = $_POST['password'];
    $passwordConfirm = $_POST['password-confirm'];
    $tokenFromDb = $user->getAttribute('passwordToken');
    if($tokenFromDb != $token)
      return $this->error('Could not validate password reset token.', false);
    elseif($password !== $passwordConfirm)
      return $this->error('Password confirmation did not match.', false);

    $this->user->update(array('password' => $password, 'passwordToken' => null));
    return $this->success('Password was updated successfully.', true);
  }

  /**
    * Update a group
    *
    * @param string $id id of the group to update
    * @return string Standard JSON envelope
    */
  public function postGroup($id = null)
  {
    getAuthentication()->requireAuthentication();

    if(!$id)
      $id = $this->user->getNextId('group');

    $res = getDb()->postGroup($id, $_POST);

    if($res)
      return $this->success("Group {$id} was updated", array_merge(array('id' => $id), $_POST));
    else
      return $this->error("Could not updated group {$id}", false);
  }
}
