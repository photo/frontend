<?php
class ApiTokenController extends ApiController
{
  private $token;

  public function __construct()
  {
    parent::__construct();
    $this->token = new Token;
  }

  public function create($type, $data)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $params = $_POST;
    $params['type'] = $type;
    $params['data'] = $data;
    $id = $this->token->create($params);
    if($id === false)
      return $this->error('Could not create share token', false);

    $tok = $this->token->get($id);
    return $this->created('Successfully created share token', $tok);
  }

  public function delete($id)
  {
    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $res = $this->token->delete($id);
    if($res === false)
      return $this->error('Could not delete share token', false);

    return $this->noContent('Successfully deleted share token', true);
  }

  public function list_()
  {
    $tokens = $this->token->getAll();
    if($tokens === false)
      return $this->error('Error getting sharing tokens', false);

    $retval = array('photos' => array(), 'albums' => array());
    foreach($tokens as $token)
    {
      if($token['type'] === 'photo')
        $retval['photos'][] = $token;
      else
        $retval['albums'][] = $token;
    }
    return $this->success('Share tokens', $retval);
  }

  public function listByTarget($type, $data)
  {
    $tokens = $this->token->getByTarget($type, $data);
    if($tokens === false)
      return $this->error('Error getting sharing tokens', false);

    return $this->success('Share tokens by target', $tokens);
  }

  public function view($id)
  {
    $tok = $this->token->get($id);
    if($tok === false)
      return $this->error('Could not get valid sharing token', false);

    return $this->success('Successfully fetched sharing token', $tok);
  }
}
