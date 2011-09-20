<?php
require_once 'PHPUnit/Framework.php';
putenv('HTTP_HOST=unittest');
require_once '../libraries/initialize.php';

class FileSystemS3Test extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->s3Stub = $this->getMock('AmazonS3', array(), array('foo', 'bar'));
    $this->sdbStub = $this->getMock('AmazonSDB', array(), array('foo', 'bar'));
  }

  public function testDeletePhotoSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new PhotoMockSdb));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $this->s3Stub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new batchResponseSuccess));
    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);

    $res = $fs->deletePhoto('foo');
    $this->assertTrue($res, 'The S3 FileSystem adapter did not return TRUE for deletePhoto');
  }

  public function testDeletePhotoFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new PhotoMockSdb));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $this->s3Stub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new batchResponseFailure));
    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);

    $res = $fs->deletePhoto('foo');
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for deletePhoto');
  }

  public function testPutPhotoSuccess()
  {
    $this->s3Stub->expects($this->any())
      ->method('create_object')
      ->will($this->returnValue(new SuccessResponse));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = getFs()->putPhoto('foo', 'bar');
    $this->assertTrue($res, 'The S3 FileSystem adapter did not return TRUE for putPhoto');
  }

  public function testPutPhotoFailure()
  {
    $this->s3Stub->expects($this->any())
      ->method('create_object')
      ->will($this->returnValue(new FailureResponse));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = getFs()->putPhoto('foo', 'bar');
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for putPhoto');
  }
}

class SuccessResponse
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


class FailureResponse
{
  public function isOK()
  {
    return false;
  }

  public function areOK()
  {
    return false;
  }
}

class BatchResponseSuccess
{
  public function delete_object()
  {
    return true;
  }

  public function send()
  {
    return new SuccessResponse;    
  }
}

class BatchResponseFailure
{
  public function delete_object()
  {
    return false;
  }

  public function send()
  {
    return new FailureResponse;    
  }
}

class PhotoMockSdb
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
