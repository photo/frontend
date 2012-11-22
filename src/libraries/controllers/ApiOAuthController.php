<?php
/**
  * OAuth controller for API endpoints.
  *
  * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class ApiOAuthController extends ApiBaseController
{
  /**
    * Call the parent constructor
    *
    * @return void
    */
  public function __construct()
  {
    parent::__construct();
  }

  public function delete($id)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $res = getDb()->deleteCredential($id);
    if($res)
      return $this->noContent('Oauth credential deleted', true);
    else
      return $this->error('Could not delete credential', false);
  }

  public function view($id)
  {
    getAuthentication()->requireAuthentication();
    $res = getDb()->getCredential($id);
    if($res !== false)
      return $this->success('Oauth Credential', $res);
    else
      return $this->error('Could not retrieve credential', false);
  }

  public function list_()
  {
    getAuthentication()->requireAuthentication();
    $res = getDb()->getCredentials();
    if($res !== false)
      return $this->success('Oauth Credentials', $res);
    else
      return $this->error('Could not retrieve credentials', false);
  }
}
