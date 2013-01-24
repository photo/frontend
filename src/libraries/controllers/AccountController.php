<?php
/**
  * Account controller for HTML endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class AccountController extends BaseController
{
  private $manageController;

  public function __construct()
  {
    parent::__construct();
    $this->manageController = new ManageController;
  }

  public function home()
  {
    $db = getDb();
    $fs = getFs();
    $params = array();
    $params['systems'] = array('FileSystem' => $this->config->systems->fileSystem);
    $params['aws'] = array('bucket' => $this->config->aws->s3BucketName);
    $params['email'] = $this->config->user->email;
    //$params['diagnostics'] = array('db' => $db->diagnostics(), 'fs' => $fs->diagnostics());
    $params['navigation'] = $this->manageController->getNavigation('account');
    $bodyTemplate = sprintf('%s/account.php', $this->config->paths->templates);
    $body = $this->template->get($bodyTemplate, $params);
    $this->theme->display('template.php', array('body' => $body, 'page' => 'account'));
  }
}

