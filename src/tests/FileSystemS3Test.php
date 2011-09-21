<?php
require_once './helpers/init.php';
require_once './helpers/aws.php';
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
      ->will($this->returnValue(new AWSPhotoMockSdb));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $this->s3Stub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchSuccessResponse));
    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);

    $res = $fs->deletePhoto('foo');
    $this->assertTrue($res, 'The S3 FileSystem adapter did not return TRUE for deletePhoto');
  }

  public function testDeletePhotoFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSPhotoMockSdb));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $this->s3Stub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchFailureResponse));
    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);

    $res = $fs->deletePhoto('foo');
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for deletePhoto');
  }

  public function testGetPhotoSuccess()
  {
    $this->s3Stub->expects($this->any())
      ->method('get_object')
      ->will($this->returnValue(new AWSSuccessResponse));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = $fs->getPhoto('foo');
    $this->assertTrue($res !== false, 'The S3 FileSystem adapter did not return TRUE for getPhoto');
  }

  public function testGetPhotoFailure()
  {
    $this->s3Stub->expects($this->any())
      ->method('get_object')
      ->will($this->returnValue(new AWSFailureResponse));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = $fs->getPhoto('foo');
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for getPhoto');
  }

  public function testPutPhotoSuccess()
  {
    $this->s3Stub->expects($this->any())
      ->method('create_object')
      ->will($this->returnValue(new AWSSuccessResponse));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = getFs()->putPhoto('foo', 'bar');
    $this->assertTrue($res, 'The S3 FileSystem adapter did not return TRUE for putPhoto');
  }

  public function testPutPhotoFailure()
  {
    $this->s3Stub->expects($this->any())
      ->method('create_object')
      ->will($this->returnValue(new AWSFailureResponse));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = getFs()->putPhoto('foo', 'bar');
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for putPhoto');
  }

  public function testPutPhotosSuccess()
  {
    $this->s3Stub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchSuccessResponse));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = getFs()->putPhotos(array(array('local1' => 'remote1'), array('local2' => 'remote2')), 'bar');
    $this->assertTrue($res, 'The S3 FileSystem adapter did not return TRUE for putPhotos');
  }

  public function testPutPhotosFailure()
  {
    $this->s3Stub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchFailureResponse));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = getFs()->putPhotos(array(array('local1' => 'remote1'), array('local2' => 'remote2')), 'bar');
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for putPhotos');
  }

  public function testGetHost()
  {
    $host = getFs()->getHost();
    $this->assertTrue(!empty($host), 'unittest', 'The S3 FileSystem adapter did not return "unittest" as the host');
  }

  public function testInitializeInvalidBucketNameCreateFailure()
  {
    $this->s3Stub->expects($this->any())
      ->method('validate_bucketname_create')
      ->will($this->returnValue(false));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = $fs->initialize();
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for initializing an invalid bucket name');
  }

  public function testInitializeInvalidBucketNameSupportFailure()
  {
    $this->s3Stub->expects($this->any())
      ->method('validate_bucketname_create')
      ->will($this->returnValue(true));
    $this->s3Stub->expects($this->any())
      ->method('validate_bucketname_support')
      ->will($this->returnValue(false));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = $fs->initialize();
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for initializing an invalid bucket name');
  }

  public function testInitializeCreateBucketFailure()
  {
    $this->s3Stub->expects($this->any())
      ->method('validate_bucketname_create')
      ->will($this->returnValue(true));
    $this->s3Stub->expects($this->any())
      ->method('validate_bucketname_support')
      ->will($this->returnValue(true));
    $this->s3Stub->expects($this->any())
      ->method('get_bucket_list')
      ->will($this->returnValue(array()));
    $this->s3Stub->expects($this->any())
      ->method('create_bucket')
      ->will($this->returnValue(new AWSFailureResponse));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = $fs->initialize();
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for initializing an bucket creation failure');
  }

  public function testInitializeCreateBucketFailedSettingPolicy()
  {
    $this->s3Stub->expects($this->any())
      ->method('validate_bucketname_create')
      ->will($this->returnValue(true));
    $this->s3Stub->expects($this->any())
      ->method('validate_bucketname_support')
      ->will($this->returnValue(true));
    $this->s3Stub->expects($this->any())
      ->method('get_bucket_list')
      ->will($this->returnValue(array()));
    $this->s3Stub->expects($this->any())
      ->method('create_bucket')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->s3Stub->expects($this->any())
      ->method('set_bucket_policy')
      ->will($this->returnValue(new AWSFailureResponse));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = $fs->initialize();
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for initializing an bucket and failure setting the policy');
  }

  public function testInitializeCreateBucketSuccess()
  {
    $this->s3Stub->expects($this->any())
      ->method('validate_bucketname_create')
      ->will($this->returnValue(true));
    $this->s3Stub->expects($this->any())
      ->method('validate_bucketname_support')
      ->will($this->returnValue(true));
    $this->s3Stub->expects($this->any())
      ->method('get_bucket_list')
      ->will($this->returnValue(array()));
    $this->s3Stub->expects($this->any())
      ->method('create_bucket')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->s3Stub->expects($this->any())
      ->method('set_bucket_policy')
      ->will($this->returnValue(new AWSSuccessResponse));

    $fs = getFs();
    $fs->inject('fs', $this->s3Stub);
    $res = $fs->initialize();
    $this->assertTrue($res, 'The S3 FileSystem adapter did not return TRUE for initializing an bucket and setting the policy');
  }
}
