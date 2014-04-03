<?php
class HttpWrapper extends Http
{
  protected function generateCommand($method, $paramsAsString, $url)
  {
    return sprintf('%s.%s.%s', $method, $paramsAsString, $url);
  }

  protected function executeCommand($command)
  {
    return array($command);
  }
}

class HttpTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->http = new HttpWrapper;
  }

  public function testFireAndForget()
  {
    $url = 'http://google.com/fake';
    $res = $this->http->fireAndForget($url);
    $this->assertEquals("'GET'..'{$url}'", $res[0], 'The return for the fireAndForget test should be current working directory');
  }

  public function testFireAndForgetWithParams()
  {
    $url = 'http://google.com/fake';
    $params = array('key' => 'value');
    $res = $this->http->fireAndForget($url, 'GET', $params);
    $this->assertEquals("'GET'.-F 'key=value' .'{$url}'", $res[0], 'The return for the fireAndForget test should be current working directory');
  }

  public function testFireAndForgetWithUserPass()
  {
    $url = 'http://google.com/fake';
    $params = array('key' => 'value', '-u' => 'user:pass');
    $res = $this->http->fireAndForget($url, 'GET', $params);
    $this->assertEquals("'GET'.-F 'key=value' -u 'user:pass' .'{$url}'", $res[0], 'The return for the fireAndForget test should be current working directory');
  }

  public function testFireAndForgetWithUserPassAsPost()
  {
    $url = 'http://google.com/fake';
    $params = array('key' => 'value', '-u' => 'user:pass');
    $res = $this->http->fireAndForget($url, 'POST', $params);
    $this->assertEquals("'POST'.-d 'key=value' -u 'user:pass' .'{$url}'", $res[0], 'The return for the fireAndForget test should be current working directory');
  }
}
