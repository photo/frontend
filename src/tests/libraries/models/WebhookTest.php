<?php
class WebhookTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->webhook = new Webhook;
  }

  public function testCreateInvalidParams()
  {
    $res = $this->webhook->create(array('foo' => 'bar'));
    $this->assertFalse($res);
  }

  public function testCreateFailure()
  {
    $db = $this->getMock('Db', array('putWebhook'));
    $db->expects($this->any())
      ->method('putWebhook')
      ->will($this->returnValue(false));
    $this->webhook->inject('db', $db);

    $res = $this->webhook->create(array('callback' => 'yes', 'topic' => 'yes'));
    $this->assertFalse($res);
  }

  public function testCreateSuccess()
  {
    $db = $this->getMock('Db', array('putWebhook'));
    $db->expects($this->any())
      ->method('putWebhook')
      ->will($this->returnValue(array('foo' => 'bar')));
    $this->webhook->inject('db', $db);

    $res = $this->webhook->create(array('callback' => 'yes', 'topic' => 'yes'));
    $this->assertEquals(32, strlen($res));
  }

  public function testDeleteFailure()
  {
    $db = $this->getMock('Db', array('deleteWebhook'));
    $db->expects($this->any())
      ->method('deleteWebhook')
      ->will($this->returnValue(false));
    $this->webhook->inject('db', $db);

    $res = $this->webhook->delete('foo');
    $this->assertFalse($res);
  }

  public function testDeleteSuccess()
  {
    $db = $this->getMock('Db', array('deleteWebhook'));
    $db->expects($this->any())
      ->method('deleteWebhook')
      ->will($this->returnValue(true));
    $this->webhook->inject('db', $db);

    $res = $this->webhook->delete('foo');
    $this->assertTrue($res);
  }

  public function testGetByIdFailure()
  {
    $db = $this->getMock('Db', array('getWebhook'));
    $db->expects($this->any())
      ->method('getWebhook')
      ->will($this->returnValue(false));
    $this->webhook->inject('db', $db);

    $res = $this->webhook->getById('foo');
    $this->assertFalse($res);
  }

  public function testGetByIdSuccess()
  {
    $db = $this->getMock('Db', array('getWebhook'));
    $db->expects($this->any())
      ->method('getWebhook')
      ->will($this->returnValue(array('foo' => 'bar')));
    $this->webhook->inject('db', $db);

    $res = $this->webhook->getById('foo');
    $this->assertEquals(array('foo' => 'bar'), $res);
  }

  public function testGetAllFailure()
  {
    $db = $this->getMock('Db', array('getWebhooks'));
    $db->expects($this->any())
      ->method('getWebhooks')
      ->will($this->returnValue(false));
    $this->webhook->inject('db', $db);

    $res = $this->webhook->getAll('foo');
    $this->assertFalse($res);
  }

  public function testGetAllSuccess()
  {
    $db = $this->getMock('Db', array('getWebhooks'));
    $db->expects($this->any())
      ->method('getWebhooks')
      ->will($this->returnValue(array('foo' => 'bar')));
    $this->webhook->inject('db', $db);

    $res = $this->webhook->getAll('foo');
    $this->assertEquals(array('foo' => 'bar'), $res);
  }

  public function testUpdateFailure()
  {
    $db = $this->getMock('Db', array('postWebhook'));
    $db->expects($this->any())
      ->method('postWebhook')
      ->will($this->returnValue(false));
    $this->webhook->inject('db', $db);

    $res = $this->webhook->update('foo', array('foo' => 'bar'));
    $this->assertFalse($res);
  }

  public function testUpdateSuccess()
  {
    $db = $this->getMock('Db', array('postWebhook'));
    $db->expects($this->any())
      ->method('postWebhook')
      ->will($this->returnValue(true));
    $this->webhook->inject('db', $db);

    $res = $this->webhook->update('foo', array('foo' => 'bar'));
    $this->assertTrue($res);
  }

  public function testGetValidAttributes()
  {
    $validAttrs = array('id' => 'foo', 'invalid' => 'somevalue');
    $res = $this->webhook->getValidAttributes($validAttrs);
    $this->assertEquals(array('id' => 'foo'), $res);
  }
}
