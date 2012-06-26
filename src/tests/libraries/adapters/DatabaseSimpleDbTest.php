<?php
class DatabaseSimpleDbOverride extends DatabaseSimpleDb
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

class DatabaseSimpleDbTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $config = array(
      'credentials' => array('awsKey' => 'foo', 'awsSecret' => 'bar'),
      'aws' => array('simpleDbDomain' => 'sdbdomain'),
      'user' => array('email' => 'test@example.com'),
      'application' => array('appId' => 'fooId')
    );
    $config = arrayToObject($config);
    $params = array('db' => true);
    $this->db = new DatabaseSimpleDbOverride($config, $params);
  }

  public function testDeleteActionSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('delete_attributes'));
    $db->expects($this->any())
      ->method('delete_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->deleteAction('foo');
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for deleteAction');
  }

  public function testDeleteActionFailure()
  {
    $db = $this->getMock('AmazonSDB', array('delete_attributes'));
    $db->expects($this->any())
      ->method('delete_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->deleteAction('foo');
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for deleteAction');
  }

  public function testDeletePhotoSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('delete_attributes'));
    $db->expects($this->any())
      ->method('delete_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->deletePhoto(array('id' => 1));
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for deletePhoto');
  }

  public function testDeletePhotoFailure()
  {
    $db = $this->getMock('AmazonSDB', array('delete_attributes'));
    $db->expects($this->any())
      ->method('delete_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->deletePhoto(array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for deletePhoto');
  }

  public function testDeletePhotoFailureWhenNoId()
  {
    $res = $this->db->deletePhoto(array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return TRUE for deletePhoto when no id attribute exiss');
  }

  public function testGetCredentialSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('select'));
    $db->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSCredentialMockSdb));
    $this->db->inject('db', $db);

    $res = $this->db->getCredential('foo');
    $this->assertEquals($res['name'], 'unittest', 'The SimpleDb adapter did not return the credential name for getCredential');
  }

  public function testGetCredentialFailure()
  {
    $db = $this->getMock('AmazonSDB', array('select'));
    $db->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->getCredential('foo');
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for getCredential');
  }

  public function testGetGroupsSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('select'));
    $db->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSGroupMockSdb(2)));
    $this->db->inject('db', $db);

    $res = $this->db->getGroups('foo');
    $this->assertEquals(count($res), 2, 'The SimpleDb adapter did not return exactly two groups for getGroups');
  }

  public function testGetGroupsFailure()
  {
    $db = $this->getMock('AmazonSDB', array('select'));
    $db->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->getGroups('foo');
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for getGroups');
  }

  public function testGetPhotoSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('select'));
    $db->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSPhotoMockSdb));
    $this->db->inject('db', $db);

    $res = $this->db->getPhoto('foo');
    $this->assertEquals($res['id'], 'foo', 'The SimpleDb adapter did not return "foo" for getPhoto');
  }

  public function testGetPhotoFailure()
  {
    $db = $this->getMock('AmazonSDB', array('select'));
    $db->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->getPhoto('foo');
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for getPhoto');
  }

  /*public function testGetPhotoNextPreviousSuccess()
  {
    // This is too difficult to test.
    // Not worth the time.
  }*/

  public function testGetPhotoNextPreviousFailure()
  {
    $db = $this->getMock('AmazonSDB', array('select'));
    $db->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->getPhotoNextPrevious('foo');
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for getPhotoNextPrevious');
  }

  /*public function testGetPhotos()
  {
    // This is too difficult to test.
    // Not worth the time.
  }*/

  public function testGetTagsSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('select'));
    $db->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSTagMockSdb(2)));
    $this->db->inject('db', $db);

    $res = $this->db->getTags();
    $this->assertEquals(count($res), 2, 'The SimpleDb adapter did not return exactly 2 tags for getTags');
    $this->assertEquals($res[0]['id'], 'foo0', 'The SimpleDb adapter did not return "foo0" as the first tag for getTags');
    $this->assertEquals($res[1]['id'], 'foo1', 'The SimpleDb adapter did not return "foo1" as the second tag for getTags');
  }

  public function testGetTagsFailure()
  {
    $db = $this->getMock('AmazonSDB', array('select'));
    $db->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->getTags();
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for getTags');
  }

  public function testGetUserSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('select'));
    $db->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSUserMockSdb));
    $this->db->inject('db', $db);

    $res = $this->db->getUser();
    $this->assertEquals($res['id'], 'foo', 'The SimpleDb adapter did not return "foo" as the id for getUser');
  }

  public function testGetUserFailure()
  {
    $db = $this->getMock('AmazonSDB', array('select'));
    $db->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->getUser();
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for getUser');
  }

  public function testInitializeSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('select', 'get_domain_list'));
    $db->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSUserMockSdb));
    $db->expects($this->any())
      ->method('get_domain_list')
      ->will($this->returnValue(array(1,2,3,4,5,6)));
    $this->db->inject('db', $db);

    $res = $this->db->initialize(false);
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for initialize when seeded with existing domains');
  }

  // TODO complete this test
  /*public function testInitializeFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('get_domain_list')
      ->will($this->returnValue(array()));
    $this->sdbStub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->initialize(false);
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for initialize');
  }*/

  public function testPostCredentialSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->postCredential('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postCredential');
  }

  public function testPostCredentialFailure()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->postCredential('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postCredential');
  }

  public function testPostGroupSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->postGroup('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postGroup');
  }

  public function testPostGroupFailure()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->postGroup('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postGroup');
  }

  public function testPostPhotoSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->postPhoto('foo', array('foo'=>'bar'));
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postPhoto');
  }

  public function testPostPhotoSuccessNoParams()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->postPhoto('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postPhoto');
  }

  public function testPostPhotoFailure()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->postPhoto('foo', array('foo'=>'bar'));
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postPhoto');
  }

  public function testPostTagSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->postTag('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postTag');
  }

  public function testPostTagFailure()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->postTag('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postTag');
  }

  public function testPostTagsSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('batch'));
    $db->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchSuccessResponse));
    $this->db->inject('db', $db);
    
    $res = $this->db->postTags(array(array('id' => 'foo')));
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postTags');
  }

  public function testPostTagsFailure()
  {
    $db = $this->getMock('AmazonSDB', array('batch'));
    $db->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->postTags(array(array('id' => 'foo')));
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postTags');
  }

  public function testPostUserSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->postUser('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postUser');
  }

  public function testPostUserFailure()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->postUser('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postUser');
  }

  public function testPutActionSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->putAction('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for putAction');
  }

  public function testPutActionFailure()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->putAction('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for putAction');
  }

  public function testPutCredentialSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->putCredential('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for putCredential');
  }

  public function testPutCredentialFailure()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->putCredential('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for putCredential');
  }

  public function testPutGroupSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->putGroup('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for putGroup');
  }

  public function testPutGroupFailure()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->putGroup('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for putGroup');
  }

  public function testPutPhotoSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->putPhoto('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for putPhoto');
  }

  public function testPutPhotoFailure()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->putPhoto('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for putPhoto');
  }

  public function testPutTagSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->putTag('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for putTag');
  }

  public function testPutTagFailure()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->putTag('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for putTag');
  }

  public function testPutUserSuccess()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $this->db->inject('db', $db);

    $res = $this->db->putUser('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for putUser');
  }

  public function testPutUserFailure()
  {
    $db = $this->getMock('AmazonSDB', array('put_attributes'));
    $db->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $this->db->inject('db', $db);

    $res = $this->db->putUser('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for putUser');
  }
}
