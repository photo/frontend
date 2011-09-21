<?php
require_once './helpers/init.php';
require_once './helpers/aws.php';
require_once '../libraries/initialize.php';

class DatabaseSimpleDbTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->sdbStub = $this->getMock('AmazonSDB', array(), array('foo', 'bar'));
  }

  public function testDeleteActionSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('delete_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $res = $db->deleteAction('foo');
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for deleteAction');
  }

  public function testDeleteActionFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('delete_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $res = $db->deleteAction('foo');
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for deleteAction');
  }

  public function testDeletePhotoSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('delete_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $res = $db->deletePhoto('foo');
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for deletePhoto');
  }

  public function testDeletePhotoFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('delete_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $res = $db->deletePhoto('foo');
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for deletePhoto');
  }

  public function testGetCredentialSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSCredentialMockSdb));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $res = $db->getCredential('foo');
    $this->assertEquals($res['name'], 'unittest', 'The SimpleDb adapter did not return the credential name for getCredential');
  }

  public function testGetCredentialFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $res = $db->getCredential('foo');
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for getCredential');
  }

  public function testGetGroupsSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSGroupMockSdb(2)));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->getGroups('foo');
    $this->assertEquals(count($res), 2, 'The SimpleDb adapter did not return exactly two groups for getGroups');
  }

  public function testGetGroupsFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $res = $db->getCredential('foo');
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for getGroups');
  }

  public function testGetPhotoSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSPhotoMockSdb));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->getPhoto('foo');
    $this->assertEquals($res['id'], 'foo', 'The SimpleDb adapter did not return "foo" for getPhoto');
  }

  public function testGetPhotoFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $res = $db->getPhoto('foo');
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for getPhoto');
  }

  public function testGetPhotoNextPreviousSuccess()
  {
    // This is too difficult to test.
    // Not worth the time.
  }

  public function testGetPhotoNextPreviousFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);

    $res = $db->getPhotoNextPrevious('foo');
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for getPhotoNextPrevious');
  }

  public function testGetPhotos()
  {
    // This is too difficult to test.
    // Not worth the time.
  }

  public function testGetTagsSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSTagMockSdb(2)));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->getTags();
    $this->assertEquals(count($res), 2, 'The SimpleDb adapter did not return exactly 2 tags for getTags');
    $this->assertEquals($res[0]['id'], 'foo0', 'The SimpleDb adapter did not return "foo0" as the first tag for getTags');
    $this->assertEquals($res[1]['id'], 'foo1', 'The SimpleDb adapter did not return "foo1" as the second tag for getTags');
  }

  public function testGetTagsFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->getTags();
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for getTags');
  }

  public function testGetUserSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSUserMockSdb));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->getUser();
    $this->assertEquals($res['id'], 'foo', 'The SimpleDb adapter did not return "foo" as the id for getUser');
  }

  public function testGetUserFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->getUser();
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for getUser');
  }

  public function testInitializeSuccessNoop()
  {
    $this->sdbStub->expects($this->any())
      ->method('get_domain_list')
      ->will($this->returnValue(array(1,2,3,4,5,6)));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->initialize();
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for initialize when seeded with existing domains');
  }

  public function testInitializeSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('get_domain_list')
      ->will($this->returnValue(array()));
    $this->sdbStub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->initialize();
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for initialize');
  }

  public function testInitializeFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('get_domain_list')
      ->will($this->returnValue(array()));
    $this->sdbStub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->initialize();
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for initialize');
  }

  public function testPostCredentialSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postCredential('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postCredential');
  }

  public function testPostCredentialFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postCredential('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postCredential');
  }

  public function testPostGroupSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postGroup('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postGroup');
  }

  public function testPostGroupFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postGroup('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postGroup');
  }

  public function testPostPhotoSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postPhoto('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postPhoto');
  }

  public function testPostPhotoFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postPhoto('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postPhoto');
  }

  public function testPostTagSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postTag('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postTag');
  }

  public function testPostTagFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postTag('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postTag');
  }

  public function testPostTagsSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postTags(array(array('id' => 'foo'))); 
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postTags');
  }

  public function testPostTagsFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postTags(array(array('id' => 'foo'))); 
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postTags');
  }

  public function testPostTagsCounterSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('select')
      ->will($this->returnValue(new AWSTagMockSdb(2)));
    $this->sdbStub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postTagsCounter(array('foo0' => 1, 'foo1' => 2)); 
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postTags');
  }

  public function testPostTagsCounterFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('batch')
      ->will($this->returnValue(new AWSBatchFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postTags(array('tag' => array('id' => 'foo'))); 
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postTags');
  }

  public function testPostUserSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postUser('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for postUser');
  }

  public function testPostUserFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->postUser('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for postUser');
  }

  public function testPutActionSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->putAction('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for putAction');
  }

  public function testPutActionFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->putAction('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for putAction');
  }

  public function testPutCredentialSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->putCredential('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for putCredential');
  }

  public function testPutCredentialFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->putCredential('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for putCredential');
  }

  public function testPutGroupSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->putGroup('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for putGroup');
  }

  public function testPutGroupFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->putGroup('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for putGroup');
  }

  public function testPutPhotoSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->putPhoto('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for putPhoto');
  }

  public function testPutPhotoFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->putPhoto('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for putPhoto');
  }

  public function testPutTagSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->putTag('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for putTag');
  }

  public function testPutTagFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->putTag('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for putTag');
  }

  public function testPutUserSuccess()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSSuccessResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->putUser('foo', array());
    $this->assertTrue($res, 'The SimpleDb adapter did not return TRUE for putUser');
  }

  public function testPutUserFailure()
  {
    $this->sdbStub->expects($this->any())
      ->method('put_attributes')
      ->will($this->returnValue(new AWSFailureResponse));
    $db = getDb();
    $db->inject('db', $this->sdbStub);
    $res = $db->putUser('foo', array());
    $this->assertFalse($res, 'The SimpleDb adapter did not return FALSE for putUser');
  }
}
