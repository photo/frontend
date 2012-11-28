<?php
class FileSystemS3Override extends FileSystemS3
{
  public function __construct($config = null, $params = null)
  {
    parent::__construct($config, $params);
  }

  public function getBatchRequest()
  {
    return null;
  }
}

class FileSystemS3Test extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $config = array(
      'credentials' => array('awsKey' => 'foo', 'awsSecret' => 'bar'),
      'aws' => array('s3BucketName' => 'foo', 's3Host' => 'bar')
    );
    $config = arrayToObject($config);
    $params = array('db' => true);
    $this->fs = new FileSystemS3Override($config, $params);
  }

  public function testDeletePhotoSuccess()
  {
    $fs = $this->getMock('AmazonS3', array('batch'));
    $fs->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchSuccessResponse));
    $this->fs->inject('fs', $fs);

    $res = $this->fs->deletePhoto(array());
    $this->assertTrue($res, 'The S3 FileSystem adapter did not return TRUE for deletePhoto');
  }

  public function testDeletePhotoDoesNotExistSuccess()
  {
    // implement this See Gh-419
  }

  public function testDeletePhotoFailure()
  {
    $fs = $this->getMock('AmazonS3', array('batch'));
    $fs->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchFailureResponse));
    
    $this->fs->inject('fs', $fs);

    $res = $this->fs->deletePhoto(array());
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for deletePhoto');
  }

  public function testGetPhotoSuccess()
  {
    $fs = $this->getMock('AmazonS3', array('get_object'));
    $fs->expects($this->any())
      ->method('get_object')
      ->will($this->returnValue(new AWSSuccessResponse));

    $this->fs->inject('fs', $fs);
    $res = $this->fs->getPhoto('foo');
    $this->assertTrue($res !== false, 'The S3 FileSystem adapter did not return TRUE for getPhoto');
  }

  public function testGetPhotoFailure()
  {
    $fs = $this->getMock('AmazonS3', array('get_object'));
    $fs->expects($this->any())
      ->method('get_object')
      ->will($this->returnValue(new AWSFailureResponse));

    $this->fs->inject('fs', $fs);
    $res = $this->fs->getPhoto('foo');
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for getPhoto');
  }

  public function testPutPhotoSuccess()
  {
    $fs = $this->getMock('AmazonS3', array('create_object'));
    $fs->expects($this->any())
      ->method('create_object')
      ->will($this->returnValue(new AWSSuccessResponse));

    $this->fs->inject('fs', $fs);
    $tempfile = sys_get_temp_dir() . '/' . rand(0,1000);
    file_put_contents($tempfile, 'foo');
    $res = $this->fs->putPhoto($tempfile, 'remote', 1234);
    $this->assertTrue($res, 'The S3 FileSystem adapter did not return TRUE for putPhoto');
  }

  public function testPutPhotoFailure()
  {
    $fs = $this->getMock('AmazonS3', array('create_object'));
    $fs->expects($this->any())
      ->method('create_object')
      ->will($this->returnValue(new AWSFailureResponse));

    $this->fs->inject('fs', $fs);
    $res = $this->fs->putPhoto('foo', 'remote', 1234);
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for putPhoto');
  }

  public function testPutPhotoDoesNotExistFailure()
  {
    $fs = $this->getMock('AmazonS3', array('create_object'));
    $fs->expects($this->any())
      ->method('create_object')
      ->will($this->returnValue(new AWSSuccessResponse));

    $this->fs->inject('fs', $fs);
    $res = $this->fs->putPhoto('foo', 'remote', 1234);
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for putPhoto');
  }

  public function testPutPhotosSuccess()
  {
    $fs = $this->getMock('AmazonS3', array('batch','create_object'));
    $fs->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchSuccessResponse));

    $this->fs->inject('fs', $fs);
    $res = $this->fs->putPhotos(array(array('local1' => array('remote1', 1234)), array('local2' => array('remote2', 1234))));
    $this->assertTrue($res, 'The S3 FileSystem adapter did not return TRUE for putPhotos');
  }

  public function testPutPhotosFailure()
  {
    $fs = $this->getMock('AmazonS3', array('batch'));
    $fs->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchFailureResponse));

    $this->fs->inject('fs', $fs);
    $res = $this->fs->putPhotos(array(array('local1' => array('remote1', 1234)), array('local2' => array('remote2', 1234))));
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for putPhotos');
  }

  public function testGetHost()
  {
    $host = $this->fs->getHost();
    $this->assertEquals($host, 'bar', 'The S3 FileSystem adapter did not return "unittest" as the host');
  }

  public function testInitializeInvalidBucketNameCreateFailure()
  {
    $fs = $this->getMock('AmazonS3', array('validate_bucketname_create'));
    $fs->expects($this->any())
      ->method('validate_bucketname_create')
      ->will($this->returnValue(false));

    $this->fs->inject('fs', $fs);
    $res = $this->fs->initialize(true);
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for initializing an invalid bucket name');
  }

  public function testInitializeInvalidBucketNameSupportFailure()
  {
    $fs = $this->getMock('AmazonS3', array('validate_bucketname_create','validate_bucketname_support'));
    $fs->expects($this->any())
      ->method('validate_bucketname_create')
      ->will($this->returnValue(true));
    $fs->expects($this->any())
      ->method('validate_bucketname_support')
      ->will($this->returnValue(false));

    $this->fs->inject('fs', $fs);
    $res = $this->fs->initialize(true);
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for initializing an invalid bucket name');
  }

  public function testInitializeCreateBucketFailure()
  {
    $fs = $this->getMock('AmazonS3', array('validate_bucketname_create','validate_bucketname_support','get_bucket_list','create_bucket'));
    $fs->expects($this->any())
      ->method('validate_bucketname_create')
      ->will($this->returnValue(true));
    $fs->expects($this->any())
      ->method('validate_bucketname_support')
      ->will($this->returnValue(true));
    $fs->expects($this->any())
      ->method('get_bucket_list')
      ->will($this->returnValue(array()));
    $fs->expects($this->any())
      ->method('create_bucket')
      ->will($this->returnValue(new AWSFailureResponse));

    $this->fs->inject('fs', $fs);
    $res = $this->fs->initialize(true);
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for initializing an bucket creation failure');
  }

  public function testInitializeCreateBucketFailedSettingPolicy()
  {
    $fs = $this->getMock('AmazonS3', array('validate_bucketname_create','validate_bucketname_support','get_bucket_list','create_bucket','set_bucket_policy'));
    $fs->expects($this->any())
      ->method('validate_bucketname_create')
      ->will($this->returnValue(true));
    $fs->expects($this->any())
      ->method('validate_bucketname_support')
      ->will($this->returnValue(true));
    $fs->expects($this->any())
      ->method('get_bucket_list')
      ->will($this->returnValue(array()));
    $fs->expects($this->any())
      ->method('create_bucket')
      ->will($this->returnValue(new AWSSuccessResponse));
    $fs->expects($this->any())
      ->method('set_bucket_policy')
      ->will($this->returnValue(new AWSFailureResponse));

    $this->fs->inject('fs', $fs);
    $res = $this->fs->initialize(true);
    $this->assertFalse($res, 'The S3 FileSystem adapter did not return FALSE for initializing an bucket and failure setting the policy');
  }

  public function testInitializeCreateBucketSuccess()
  {
    $fs = $this->getMock('AmazonS3', array('validate_bucketname_create','validate_bucketname_support','get_bucket_list','create_bucket','set_bucket_policy'));
    $fs->expects($this->any())
      ->method('validate_bucketname_create')
      ->will($this->returnValue(true));
    $fs->expects($this->any())
      ->method('validate_bucketname_support')
      ->will($this->returnValue(true));
    $fs->expects($this->any())
      ->method('get_bucket_list')
      ->will($this->returnValue(array()));
    $fs->expects($this->any())
      ->method('create_bucket')
      ->will($this->returnValue(new AWSSuccessResponse));
    $fs->expects($this->any())
      ->method('set_bucket_policy')
      ->will($this->returnValue(new AWSSuccessResponse));

    $this->fs->inject('fs', $fs);
    $res = $this->fs->initialize(true);
    $this->assertTrue($res, 'The S3 FileSystem adapter did not return TRUE for initializing an bucket and setting the policy');
  }
}
