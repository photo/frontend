<?php
class ResourceModelTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->resourceMap = new ResourceMap;
  }

  public function testCreateSuccess()
  {
    $user = $this->getMock('Db', array('getNextId'));
    $user->expects($this->any())
      ->method('getNextId')
      ->will($this->returnValue('abc'));
    $db = $this->getMock('Db', array('putResourceMap'));
    $db->expects($this->any())
      ->method('putResourceMap')
      ->will($this->returnValue(true));
    $this->resourceMap->inject('db', $db);
    $this->resourceMap->inject('user', $user);
    $res = $this->resourceMap->create(array('uri' => '/foo/bar', 'method' => 'GET'));
    $this->assertEquals('abc', $res);
  }

  public function testCreateCouldNotGetNextIdFailure()
  {
    $user = $this->getMock('Db', array('getNextId'));
    $user->expects($this->any())
      ->method('getNextId')
      ->will($this->returnValue(false));
    $db = $this->getMock('Db', array('putResourceMap'));
    $db->expects($this->any())
      ->method('putResourceMap')
      ->will($this->returnValue(true));
    $this->resourceMap->inject('db', $db);
    $this->resourceMap->inject('user', $user);
    $res = $this->resourceMap->create(array('uri' => '/foo/bar', 'method' => 'GET'));
    $this->assertFalse($res);
  }

  public function testCreateCouldNotInsertToDbFailure()
  {
    $user = $this->getMock('Db', array('getNextId'));
    $user->expects($this->any())
      ->method('getNextId')
      ->will($this->returnValue('abc'));
    $db = $this->getMock('Db', array('putResourceMap'));
    $db->expects($this->any())
      ->method('putResourceMap')
      ->will($this->returnValue(false));
    $this->resourceMap->inject('db', $db);
    $this->resourceMap->inject('user', $user);
    $res = $this->resourceMap->create(array('uri' => '/foo/bar', 'method' => 'GET'));
    $this->assertFalse($res);
  }
}
