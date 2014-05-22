<?php
class NotificationTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $user = $this->getMock('user', array('getEmailAddress'), array(), '', false);
    $user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('test@example.com'));
    $this->notification = new Notification(array('user' => $user));
    $this->cache = $this->getMock('cache', array('get','set','delete'), array(), '', false);
  }

  public function testAddSuccess()
  {
    $msg = 'foobar ' . time();
    $this->cache->expects($this->any())
      ->method('get')
      ->will($this->returnValue($this->cacheResponse($msg)));
    $this->cache->expects($this->any())
      ->method('set')
      ->will($this->returnValue($this->cacheResponse($msg)));
    $this->notification->inject('cache', $this->cache);
    $res = $this->notification->add($msg);
    $this->assertEquals($this->cacheResponse($msg), $res, 'The add method should return the value passed in');
  }

  public function testAddThenGetSuccess()
  {
    $msg = 'foobar ' . time();
    $this->cache->expects($this->any())
      ->method('set')
      ->will($this->returnValue($this->cacheResponse($msg)));
    $this->cache->expects($this->any())
      ->method('get')
      ->will($this->returnValue($this->cacheResponse($msg)));
    $this->notification->inject('cache', $this->cache);
    $this->notification->add($msg);
    $res = $this->notification->get();
    $this->assertEquals(array('type' => Notification::typeFlash, 'msg' => $msg, 'mode' => Notification::modeConfirm), $res, 'After adding the get method should return the value set');
  }

  public function testGetClearsFlashQueueSuccess()
  {
    $msg = 'foobar ' . time();
    $this->cache = $this->getMock('cache', array('get','set','delete'));
    $this->cache->expects($this->any())
      ->method('set')
      ->will($this->returnValue($this->cacheResponse($msg)));
    $this->cache->expects($this->any())
      ->method('get')
      ->will($this->onConsecutiveCalls(
        $this->cacheResponse($msg),
        null
      ));
    $this->notification->inject('cache', $this->cache);
    $this->notification->add($msg);
    $this->notification->get();
    $res = $this->notification->get();
    $this->assertNull($res, 'Calling get should clear the messages');
  }

  public function testGetStaticDoesNotCallSetSuccess()
  {
    $msg = 'foobar ' . time();
    $this->cache = $this->getMock('cache', array('get'));
    $this->cache->expects($this->any())
      ->method('get')
      ->will($this->onConsecutiveCalls(
        $this->cacheResponse($msg, Notification::typeStatic),
        null
      ));
    $this->notification->inject('cache', $this->cache);
    $this->notification->get();
    $res = $this->notification->get();
    $this->assertNull($res, 'Calling get should clear the messages');
  }

  public function testGetAndRemoveFromFlashSuccess()
  {
    $queue = $this->cacheResponse(array('one', 'two'));
    $res = $this->notification->getAndRemoveFrom($queue, Notification::typeFlash);
    $this->assertEquals($res, array('msg' => 'one', 'mode' => Notification::modeConfirm));
    $this->assertEquals($this->cacheResponse(array('two')), $queue);
  }

  public function testGetAndRemoveFromFlashWithStaticSuccess()
  {
    $queue = $this->cacheResponse(array('one', 'two'));
    $queue[Notification::typeStatic][] = 'hi';
    $res = $this->notification->getAndRemoveFrom($queue, Notification::typeFlash);
    $this->assertEquals($res, array('msg' => 'one', 'mode' => Notification::modeConfirm));

    $queueExp = $this->cacheResponse(array('two'));
    $queueExp[Notification::typeStatic][] = 'hi';
    $this->assertEquals($queueExp, $queue);
  }

  public function testGetAndRemoveFromStaticSuccess()
  {
    $queue = $this->cacheResponse(array('one', 'two'), Notification::typeStatic);
    $res = $this->notification->getAndRemoveFrom($queue, Notification::typeStatic);
    $this->assertEquals($res, array('msg' => 'one', 'mode' => Notification::modeConfirm));
    $this->assertEquals($this->cacheResponse(array('two'), Notification::typeStatic), $queue);
  }

  public function testGetAndRemoveFromStaticWithFlashSuccess()
  {
    $queue = $this->cacheResponse(array('one', 'two'), Notification::typeStatic);
    $queue[Notification::typeFlash][] = 'hi';
    $res = $this->notification->getAndRemoveFrom($queue, Notification::typeStatic);
    $this->assertEquals($res, array('msg' => 'one', 'mode' => Notification::modeConfirm));

    $queueExp = $this->cacheResponse(array('two'), Notification::typeStatic);
    $queueExp[Notification::typeFlash][] = 'hi';
    $this->assertEquals($queueExp, $queue);
  }

  public function testGetForceFlashSuccess()
  {
    $queue = $this->cacheResponse(array('one', 'two'));
    $queue[Notification::typeStatic][] = 'hi';
    $this->cache->expects($this->any())
      ->method('get')
      ->will($this->returnValue($queue));
    $this->cache->expects($this->any())
      ->method('set')
      ->will($this->returnValue($queue));
    $this->notification->inject('cache', $this->cache);
    $res = $this->notification->get(Notification::typeFlash);
    $this->assertEquals(array('msg' => 'one', 'type' => Notification::typeFlash, 'mode' => Notification::modeConfirm), $res);
  }

  public function testGetForceStaticSuccess()
  {
    $queue = $this->cacheResponse(array('one', 'two'));
    $queue[Notification::typeStatic][] = array('msg' => 'hi', 'mode' => Notification::modeConfirm);
    $this->cache->expects($this->any())
      ->method('get')
      ->will($this->returnValue($queue));
    $this->cache->expects($this->any())
      ->method('set')
      ->will($this->returnValue($queue));
    $this->notification->inject('cache', $this->cache);
    $res = $this->notification->get(Notification::typeStatic);
    $this->assertEquals(array('msg' => 'hi', 'type' => Notification::typeStatic, 'mode' => Notification::modeConfirm), $res);
  }

  public function testGetFallbackToStaticWhenFlashExistsSuccess()
  {
    $queue = $this->cacheResponse(array('one', 'two'), Notification::typeFlash);
    $this->cache->expects($this->any())
      ->method('get')
      ->will($this->returnValue($queue));
    $this->cache->expects($this->any())
      ->method('set')
      ->will($this->returnValue($queue));
    $this->notification->inject('cache', $this->cache);
    $res = $this->notification->get();
    $this->assertEquals(array('msg' => 'one', 'type' => Notification::typeFlash, 'mode' => Notification::modeConfirm), $res);
  }

  public function testGetFallbackToStaticWhenNoFlashSuccess()
  {
    $queue = $this->cacheResponse(array('one', 'two'), Notification::typeStatic);
    $this->cache->expects($this->any())
      ->method('get')
      ->will($this->returnValue($queue));
    $this->cache->expects($this->any())
      ->method('set')
      ->will($this->returnValue($queue));
    $this->notification->inject('cache', $this->cache);
    $res = $this->notification->get();
    $this->assertEquals(array('msg' => 'one', 'type' => Notification::typeStatic, 'mode' => Notification::modeConfirm), $res);
  }

  private function cacheResponse($msg, $type = Notification::typeFlash)
  {
    $retval = array(Notification::typeFlash => array(), Notification::typeStatic => array());
    foreach((array)$msg as $m)
      $retval[$type][] = array('msg' => $m, 'mode' => Notification::modeConfirm);
    return $retval;
  }
}
