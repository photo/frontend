<?php
class ActionTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    // to test the write methods
    $this->action = new Action(array('user'=>new stdClass));
    $this->action->config = json_decode(json_encode(array('application' => array('appId' => 'foo'), 'user' => array('email' => 'bar'))));
  }

  public function testCreate()
  {
    $user = $this->getMock('User', array('getNextId'));
    $user->expects($this->any())
      ->method('getNextId')
      ->will($this->returnValue('a'));
    $this->action->inject('user', $user);

    $db = $this->getMock('Db', array('putAction'));
    $db->expects($this->any())
      ->method('putAction')
      ->will($this->returnValue('a'));
    $this->action->inject('db', $db);

    $params = array('foo' => 'bar');
    $res = $this->action->create($params);
    $this->assertFalse($res, 'Action::create requires type and targetType');

    $params['type'] = $params['targetType'] = 'foo';
    $res = $this->action->create($params);
    $this->assertEquals('a', $res, 'Action::create should return id on success');
  }

  public function testDelete()
  {
    $db = $this->getMock('Db', array('deleteAction'));
    $db->expects($this->any())
      ->method('deleteAction')
      ->will($this->returnValue(true));
    $this->action->inject('db', $db);

    $res = $this->action->delete('a');
    $this->assertTrue($res, 'successful delete should return true');

    $db = $this->getMock('Db', array('deleteAction'));
    $db->expects($this->any())
      ->method('deleteAction')
      ->will($this->returnValue(false));
    $this->action->inject('db', $db);

    $res = $this->action->delete('a');
    $this->assertFalse($res, 'failed delete should return false');
  }

  public function testView()
  {
    $response = array('id' => 'foo', 'bar' => 'example');
    $db = $this->getMock('Db', array('getAction'));
    $db->expects($this->any())
      ->method('getAction')
      ->will($this->returnValue($response));
    $this->action->inject('db', $db);

    $res = $this->action->view('a');
    $this->assertEquals($response, $res, 'view should return action object');
  }
}
