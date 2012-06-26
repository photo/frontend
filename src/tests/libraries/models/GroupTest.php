<?php
class GroupTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->group = new Group(array('user' => new FauxObject));
    $this->group->config = json_decode(json_encode(array('application' => array('appId' => 'foo'), 'user' => array('email' => 'bar'))));
  }

  public function testCreateNotValidated()
  {
    $res = $this->group->create(array());
    $this->assertFalse($res, 'An empty array should not pass validation and return FALSE');

    $res = $this->group->create(array('appId' => 'foo'));
    $this->assertFalse($res, 'With "name" missing should not pass validation and return FALSE');
  }

  public function testCreateCouldNotGetNextId()
  {
    $user = $this->getMock('User', array('getNextId'));
    $user->expects($this->any())
      ->method('getNextId')
      ->will($this->returnValue(false));
    $this->group->inject('user', $user);

    $res = $this->group->create(array('appId' => 'foo', 'name' => 'bar'));
    $this->assertFalse($res, 'When the next id cannot be retrieved it should return FALSE');
  }

  public function testCreateFailure()
  {
    $user = $this->getMock('User', array('getNextId'));
    $user->expects($this->any())
      ->method('getNextId')
      ->will($this->returnValue('a'));
    $db = $this->getMock('Db', array('putGroup'));
    $db->expects($this->any())
      ->method('putGroup')
      ->will($this->returnValue(false));
    $this->group->inject('user', $user);
    $this->group->inject('db', $db);

    $res = $this->group->create(array('appId' => 'foo', 'name' => 'bar'));
    $this->assertFalse($res, 'When posting to db fails it should return FALSE');
  }

  public function testCreateSuccess()
  {
    $user = $this->getMock('User', array('getNextId'));
    $user->expects($this->any())
      ->method('getNextId')
      ->will($this->returnValue('abc'));
    $db = $this->getMock('Db', array('putGroup'));
    $db->expects($this->any())
      ->method('putGroup')
      ->will($this->returnValue(true));
    $this->group->inject('user', $user);
    $this->group->inject('db', $db);

    $res = $this->group->create(array('appId' => 'foo', 'name' => 'bar'));
    $this->assertEquals('abc', $res, 'A successful creation should return the id');
  }

  public function testDeleteFailure()
  {
    $db = $this->getMock('Db', array('deleteGroup'));
    $db->expects($this->any())
      ->method('deleteGroup')
      ->will($this->returnValue(false));
    $this->group->inject('db', $db);

    $res = $this->group->delete('abc');
    $this->assertFalse($res, 'When delete fails it should return FALSE');
  }

  public function testDeleteSuccess()
  {
    $db = $this->getMock('Db', array('deleteGroup'));
    $db->expects($this->any())
      ->method('deleteGroup')
      ->will($this->returnValue(true));
    $this->group->inject('db', $db);

    $res = $this->group->delete('abc');
    $this->assertTrue($res, 'When delete fails it should return TRUE');
  }

  public function testGetGroupFailure()
  {
    $db = $this->getMock('Db', array('getGroup'));
    $db->expects($this->any())
      ->method('getGroup')
      ->will($this->returnValue(false));
    $this->group->inject('db', $db);

    $res = $this->group->getGroup('abc');
    $this->assertFalse($res, 'When db->getGroup returns FALSE group->getGroup should also');
  }

  public function testGetGroupSuccess()
  {
    $return = array('foo' => 'bar');
    $db = $this->getMock('Db', array('getGroup'));
    $db->expects($this->any())
      ->method('getGroup')
      ->will($this->returnValue($return));
    $this->group->inject('db', $db);

    $res = $this->group->getGroup('abc');
    $this->assertEquals($return, $res, 'When successful getGroup from group should return what db returns');
  }

  public function testGetGroupsFailure()
  {
    $db = $this->getMock('Db', array('getGroups'));
    $db->expects($this->any())
      ->method('getGroups')
      ->will($this->returnValue(false));
    $this->group->inject('db', $db);

    $res = $this->group->getGroups();
    $this->assertFalse($res, 'When db->getGroups returns FALSE group->getGroups should also');
  }

  public function testGetGroupsSuccess()
  {
    $return = array('foo' => 'bar');
    $db = $this->getMock('Db', array('getGroups'));
    $db->expects($this->any())
      ->method('getGroups')
      ->will($this->returnValue($return));
    $this->group->inject('db', $db);

    $res = $this->group->getGroups();
    $this->assertEquals($return, $res, 'When successful getGroups from group should return what db returns');
  }

  public function testUpdateNotValidated()
  {
    $res = $this->group->update('a', array());
    $this->assertFalse($res, 'An empty array should not pass validation and return FALSE');

    $res = $this->group->update('a', array('appId' => 'foo'));
    $this->assertFalse($res, 'With "name" missing should not pass validation and return FALSE');
  }

  public function testUpdateFailure()
  {
    $db = $this->getMock('Db', array('postGroup'));
    $db->expects($this->any())
      ->method('postGroup')
      ->will($this->returnValue(false));
    $this->group->inject('db', $db);

    $res = $this->group->update('a', array('name' => 'foobar'));
    $this->assertFalse($res, 'When an update fails FALSE should be returned');
  }

  public function testUpdateSuccess()
  {
    $db = $this->getMock('Db', array('postGroup'));
    $db->expects($this->any())
      ->method('postGroup')
      ->will($this->returnValue(true));
    $this->group->inject('db', $db);

    $res = $this->group->update('a', array('name' => 'foobar'));
    $this->assertTrue($res, 'When an update succeeds TRUE should be returned');
  }
}
