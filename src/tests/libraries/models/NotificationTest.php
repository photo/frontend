<?php
class NotificationTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->notification = new Notification;
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
    $this->assertEquals(array('type' => Notification::typeFlash, 'msg' => $msg), $res, 'After adding the get method should return the value set');
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
    $res = $this->notification->getAndRemoveFromFlash($queue);
    $this->assertEquals($res, 'one');
    $this->assertEquals($this->cacheResponse(array('two')), $queue);
  }

  public function testGetAndRemoveFromFlashWithStaticSuccess()
  {
    $queue = $this->cacheResponse(array('one', 'two'));
    $queue[Notification::typeStatic][] = 'hi';
    $res = $this->notification->getAndRemoveFromFlash($queue);
    $this->assertEquals($res, 'one');

    $queueExp = $this->cacheResponse(array('two'));
    $queueExp[Notification::typeStatic][] = 'hi';
    $this->assertEquals($queueExp, $queue);
  }

  private function cacheResponse($msg, $type = Notification::typeFlash)
  {
    $retval = array(Notification::typeFlash => array(), Notification::typeStatic => array());
    foreach((array)$msg as $m)
      $retval[$type][] = $m;
    return $retval;
  }
}
