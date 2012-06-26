<?php
class AmazonS3
{
  const ACL_PUBLIC = 1;
  const ACL_PRIVATE = 1;
  const REGION_US_E1 = 1;
}

class CFPolicy
{

}

class AWSSuccessResponse
{
  public function isOK()
  {
    return true;
  }

  public function areOK()
  {
    return true;
  }
}


class AWSFailureResponse
{
  public $body;
  public function __construct()
  {
    $this->body = new stdClass;
    $this->body->Errors = array();
  }

  public function isOK()
  {
    return false;
  }

  public function areOK()
  {
    return false;
  }
}

class AWSBatchSuccessResponse extends AWSSuccessResponse
{
  private $type, $count;
  public function __construct($type = null, $count = 0)
  {
    $this->count = $count;
    switch($type)
    {
      case 'photo':
        $this->type = 'photo';
        break;
    }
  }

  public function delete_object()
  {
    return true;
  }

  public function send()
  {
    return $this->returnByType();
  }

  public function create_domain()
  {
    return new AWSSuccessResponse;
  }

  public function create_object()
  {
    return new AWSSuccessResponse;
  }

  public function put_attributes()
  {
    return true;
  }

  public function select()
  {
    return $this->returnByType();
  }

  private function returnByType()
  {
    $retval = null;
    switch($this->type)
    {
      case 'photo':
        $retval = array(new AWSPhotoMockSdb($this->count));
        break;
      default:
        $retval = new AWSSuccessResponse;
        break;
    }
    return $retval;
  }
}

class AWSBatchFailureResponse extends AWSFailureResponse
{
  public function delete_object()
  {
    return false;
  }

  public function create_domain()
  {
    return new AWSFailureResponse;
  }

  public function send()
  {
    return new AWSFailureResponse;
  }

  public function put_attributes()
  {
    return false;
  }

  public function create_object()
  {
    return new AWSFailureResponse;
  }
}

class AWSCredentialMockSdb extends AWSSuccessResponse
{
  public $body;
  public function __construct()
  {
    $this->body = new stdClass;
    $this->body->SelectResult = new stdClass;
    $this->body->SelectResult->Item = new stdClass;
    $this->body->SelectResult->Item->Name = 'foo';
    $this->body->SelectResult->Item->Attribute = array(
      $this->attr('name', 'unittest'),
      $this->attr('clientSecret', 'clientSecret'),
      $this->attr('userToken', 'userToken')
    );

  }

  private function attr($name, $value)
  {
    $ret = new stdClass;
    $ret->Name = $name;
    $ret->Value = $value;
    return $ret;
  }
}

class AWSGroupMockSdb extends AWSSuccessResponse
{
  public $body;
  public function __construct($count = 1)
  {
    $this->body = new stdClass;
    $this->body->SelectResult = new stdClass;
    if($count == 1)
    {
      $this->body->SelectResult->Item = new stdClass;
      $this->body->SelectResult->Item->Name = 'foo';
      $this->body->SelectResult->Item->Attribute = array(
        $this->attr('name', 'clientSecret'),
        $this->attr('members', array('user@test.com', 'foo@bar.com'))
      );
    }
    elseif($count > 1)
    {
      $this->body->SelectResult->Item = array();
      for($i=0; $i<$count; $i++)
      {
        $this->body->SelectResult->Item[$i] = new stdClass;
        $this->body->SelectResult->Item[$i]->Name = 'foo';
        $this->body->SelectResult->Item[$i]->Attribute = array(
          $this->attr('name', 'clientSecret'),
          $this->attr('members', array('user@test.com', 'foo@bar.com'))
        );
      }
    }
  }

  private function attr($name, $value)
  {
    $ret = new stdClass;
    $ret->Name = $name;
    $ret->Value = $value;
    return $ret;
  }
}

class AWSPhotoMockSdb extends AWSSuccessResponse
{
  public $body;
  public function __construct()
  {
    $this->body = new stdClass;
    $this->body->SelectResult = new stdClass;
    $this->body->SelectResult->Item = new stdClass;
    $this->body->SelectResult->Item->Name = 'foo';
    $this->body->SelectResult->Item->Attribute = array(
      $this->attr('host', 'unittest'),
      $this->attr('dateTaken', time())
    );

  }

  private function attr($name, $value)
  {
    $ret = new stdClass;
    $ret->Name = $name;
    $ret->Value = $value;
    return $ret;
  }
}

class AWSTagMockSdb extends AWSSuccessResponse
{
  public $body;
  public function __construct($count = 1)
  {
    $this->body = new stdClass;
    $this->body->SelectResult = new stdClass;
    if($count == 1)
    {
      $this->body->SelectResult->Item = new stdClass;
      $this->body->SelectResult->Item->Name = 'foo';
      $this->body->SelectResult->Item->Attribute = array(
        $this->attr('count', 1)
      );
    }
    elseif($count > 1)
    {
      $this->body->SelectResult->Item = array();
      for($i=0; $i<$count; $i++)
      {
        $this->body->SelectResult->Item[$i] = new stdClass;
        $this->body->SelectResult->Item[$i]->Name = "foo{$i}";
        $this->body->SelectResult->Item[$i]->Attribute = array(
          $this->attr('count', 1)
        );
      }
    }
  }

  private function attr($name, $value)
  {
    $ret = new stdClass;
    $ret->Name = $name;
    $ret->Value = $value;
    return $ret;
  }
}

class AWSUserMockSdb extends AWSSuccessResponse
{
  public $body;
  public function __construct()
  {
    $this->body = new stdClass;
    $this->body->SelectResult = new stdClass;
    $this->body->SelectResult->Item = new stdClass;
    $this->body->SelectResult->Item->Name = 'foo';
    $this->body->SelectResult->Item->Attribute = array(
      $this->attr('lastPhotoId', 1),
      $this->attr('lastActionId', 2),
      $this->attr('version', '1.0.0')
    );
  }

  private function attr($name, $value)
  {
    $ret = new stdClass;
    $ret->Name = $name;
    $ret->Value = $value;
    return $ret;
  }
}
