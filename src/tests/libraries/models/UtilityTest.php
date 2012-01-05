<?php
$baseDir = dirname(dirname(dirname(dirname(__FILE__))));
require_once sprintf('%s/tests/helpers/init.php', $baseDir);
require_once sprintf('%s/libraries/models/Utility.php', $baseDir);
require_once sprintf('%s/libraries/models/Url.php', $baseDir);

class EpiRoute
{
  const httpGet = 'GET';
  const httpPost = 'POST';
}

class UtilityTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->utility = new Utility;
  }

  public function testCallApisOnce()
  {
    $response = array('code' => 200, 'message' => 'some message', 'result' => array(1));
    $mockApi = $this->getMock('ApiMock', array('invoke'));
    $mockApi->expects($this->any())
      ->method('invoke')
      ->will($this->returnValue($response));
    $apis = array(
      'test' => 'GET /photos/list.json?pageSize=20&returnSizes=800x450xCR'
    );
    $res = $this->utility->callApis($apis, $mockApi);
    $this->assertEquals($res['test'], $response['result'], 'When calling a single api callApis does not return correct response');
  }

  public function testCallApisTwice()
  {
    $response1 = array('code' => 200, 'message' => 'some message 1', 'result' => array(1));
    $response2 = array('code' => 200, 'message' => 'some message 2', 'result' => array(2));
    $mockApi = $this->getMock('ApiMock', array('invoke'));
    $mockApi->expects($this->at(0))
      ->method('invoke')
      ->will($this->returnValue($response1));
    $mockApi->expects($this->at(1))
      ->method('invoke')
      ->will($this->returnValue($response2));
    $apis = array(
      'test1' => 'GET /photos/list.json?pageSize=20&returnSizes=800x450xCR',
      'test2' => 'GET /photos/list.json?pageSize=20&returnSizes=800x450xCR'
    );
    $res = $this->utility->callApis($apis, $mockApi);
    $this->assertEquals($res['test1'], $response1['result'], 'When calling two apis callApis does not return the correct first response');
    $this->assertEquals($res['test2'], $response2['result'], 'When calling two apis callApis does not return the correct second response');
  }

  public function testCallApisNone()
  {
    $res = $this->utility->callApis(null, 1/*not null*/);
    $this->assertEquals($res, array(), 'When passing null an empty array should be returned');
  }

  public function testDecrypt()
  {
    $res = $this->utility->decrypt('string', 'secret', 'salt');
    $this->assertEquals('lOHFbOH4AD+cpcRh1FcDte9DAapMDzqIHrwFz5DvxD4=', base64_encode($res), 'decrypted string is not correct');
  }

  public function testDiagnosticLine()
  {
    $res = $this->utility->diagnosticLine(true, 'message');
    $this->assertEquals(array('status' => true, 'label' => 'success', 'message' => 'message'), $res, 'success diagnostic line not correct');

    $res = $this->utility->diagnosticLine(1, 'message');
    $this->assertEquals(array('status' => true, 'label' => 'success', 'message' => 'message'), $res, 'failure diagnostic line not correct when using 1');

    $res = $this->utility->diagnosticLine(false, 'message');
    $this->assertEquals(array('status' => false, 'label' => 'failure', 'message' => 'message'), $res, 'failure diagnostic line not correct');

    $res = $this->utility->diagnosticLine(0, 'message');
    $this->assertEquals(array('status' => false, 'label' => 'failure', 'message' => 'message'), $res, 'failure diagnostic line not correct when using 0');
  }

  public function testEncrypt()
  {
    $res = $this->utility->encrypt('string', 'secret', 'salt');
    $this->assertEquals('b2ljK3AzRVE3Tk56N0FNd3dlUFRLMWRHaHh3QnVReG8wdFRHdktYczRFWT0=', base64_encode($res), 'encrypted string is not correct');
  }

  public function testGetBaseDir()
  {
    $res = $this->utility->getBaseDir();
    $this->assertEquals(dirname(dirname(dirname(dirname(__FILE__)))), $res, 'getBaseDir not correct');
  }
}
