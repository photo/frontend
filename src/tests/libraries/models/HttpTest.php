<?php
class HttpWrapper extends Http
{
  protected function generateCommand($method, $paramsAsString, $url)
  {
    return 'pwd';
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
    exec('pwd', $cwd);
    $cwd = $cwd[0];
    $res = $this->http->fireAndForget('http://google.com/fake');
    $this->assertEquals($cwd, $res[0], 'The return for the fireAndForget test should be current working directory');
  }
}
