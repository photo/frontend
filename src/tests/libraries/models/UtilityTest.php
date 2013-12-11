<?php
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

  public function testDecreaseGeolocationPrecisionRoundUp()
  {
    $res = $this->utility->decreaseGeolocationPrecision(12345.6789);
    $this->assertEquals(12346, $res, 'decrypted string is not correct');
  }

  public function testDecreaseGeolocationPrecisionRoundDown()
  {
    $res = $this->utility->decreaseGeolocationPrecision(12345.45678);
    $this->assertEquals(12345, $res, 'decrypted string is not correct');
  }

  public function testDecrypt()
  {
    if (!extension_loaded('mcrypt')) {
      $this->markTestSkipped("Test requires ext/mcrypt");
      return;
    }
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
    if (!extension_loaded('mcrypt')) {
      $this->markTestSkipped("Test requires ext/mcrypt");
      return;
    }
    $res = $this->utility->encrypt('string', 'secret', 'salt');
    $this->assertEquals('b2ljK3AzRVE3Tk56N0FNd3dlUFRLMWRHaHh3QnVReG8wdFRHdktYczRFWT0=', base64_encode($res), 'encrypted string is not correct');
  }

  public function testGetBaseDir()
  {
    $res = $this->utility->getBaseDir();
    $this->assertEquals(dirname(dirname(dirname(dirname(__FILE__)))), $res, 'getBaseDir not correct');
  }

  public function testGetLicenses()
  {
    $licenses = $this->utility->getLicenses();
    $this->assertTrue(isset($licenses['']), 'no empty license as first option');
    $this->assertEquals(7, count($licenses), 'There should be 7 licenses returned');
  }

  public function testGetLicensesSelected()
  {
    $licenses = $this->utility->getLicenses('CC BY');
    $this->assertTrue($licenses['CC BY']['selected'], 'CC BY should be selected');
    $this->assertFalse($licenses['CC BY-SA']['selected'], 'CC BY-SA should NOT be selected');
  }

  public function testDateLong()
  {
    date_default_timezone_set('America/Los_Angeles');
    $res = $this->utility->dateLong(1325810036, false);
    $this->assertEquals('Thursday, January 5th, 2012 at 4:33pm', $res, 'Date format does not match expected value');
  }

  public function testDateLongEmpty()
  {
    date_default_timezone_set('America/Los_Angeles');
    $res = $this->utility->dateLong(0, false);
    $this->assertEquals('Unknown', $res, 'Date format does not match expected value for 0');

    $res = $this->utility->dateLong('', false);
    $this->assertEquals('Unknown', $res, 'Date format does not match expected value for empty string');

    $res = $this->utility->dateLong(null, false);
    $this->assertEquals('Unknown', $res, 'Date format does not match expected value for null');
  }

  public function testGenerateIniString()
  {
    $res = $this->utility->generateIniString(array('foo' => array('bar' => 'value')), true);
    $expected = <<<RES
[foo]
bar = "value"
RES;
    
    $this->assertEquals($expected, $res, 'Ini string with sections failed');
  }

  public function testGetEmailHandle()
  {
    $res = $this->utility->getEmailHandle('user@example.com', false);
    $this->assertEquals('user', $res);
    $res = $this->utility->getEmailHandle('user+one@example.com', false);
    $this->assertEquals('user+one', $res);
    $res = $this->utility->getEmailHandle('user+.-)(*&^%$#!@example.com', false);
    $this->assertEquals('user+.-)(*&^%$#!', $res);
  }

  public function testGetProtocol()
  {
    $_SERVER['HTTPS'] = null;
    $res = $this->utility->getProtocol(false);
    $this->assertEquals('http', $res);

    $_SERVER['HTTPS'] = 'on';
    $res = $this->utility->getProtocol(false);
    $this->assertEquals('https', $res);

    $_SERVER['HTTPS'] = 'On';
    $res = $this->utility->getProtocol(false);
    $this->assertEquals('https', $res);

    $_SERVER['HTTPS'] = null;

    $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'http';
    $res = $this->utility->getProtocol(false);
    $this->assertEquals('http', $res);

    $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
    $res = $this->utility->getProtocol(false);
    $this->assertEquals('https', $res);

    $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'HTTPS';
    $res = $this->utility->getProtocol(false);
    $this->assertEquals('https', $res);
  }

  public function testIsActiveTab()
  {
    $_GET['__route__'] = '/';
    $res = $this->utility->isActiveTab('home');
    $this->assertTrue($res, 'home is not active tab');

    $_GET['__route__'] = '/photo/view';
    $res = $this->utility->isActiveTab('photo');
    $this->assertTrue($res, '/photo/view not photo tab');

    $_GET['__route__'] = '/photos/one/two/three';
    $res = $this->utility->isActiveTab('photo');
    $this->assertFalse($res);

    $_GET['__route__'] = '/photos/upload';
    $res = $this->utility->isActiveTab('photo');
    $this->assertFalse($res);

    $_GET['__route__'] = '/tags/list';
    $res = $this->utility->isActiveTab('tags');
    $this->assertTrue($res, '/tags/list not tags tab');

    $_GET['__route__'] = '/photos/upload';
    $res = $this->utility->isActiveTab('upload');
    $this->assertTrue($res, '/photos/upload not upload tab');
  }

  public function testGetHostSuccess()
  {
    $_SERVER['HTTP_HOST'] = 'foobar';
    $res = $this->utility->getHost();
    $this->assertEquals('foobar', $res);
  }

  public function testGetNewHostSuccess()
  {
    $this->markTestIncomplete('This test has not been implemented yet.');
    /*$_SERVER['HTTP_HOST'] = 'foo.base.com';

    $this->config = new stdClass;
    $this->config->site = new stdClass;
    $this->config->site->baseHost = 'base.com';
    $this->config->site->rewriteHost = 'rewrite.com';

    $res = $this->utility->getHost(true);
    $this->assertEquals('foo.rewrite.com', $res);*/
  }
  
  // TODO implement some sort of test
  public function testIsMobile() {}

  // TODO implement some sort of test
  public function testGetTemplate() {}

  public function testLicenseLong()
  {
    $res = $this->utility->licenseLong('CC BY', false);
    $this->assertEquals('CC BY (Attribution)', $res, 'CC BY license not properly retrieved');

    $res = $this->utility->licenseLong('Does not exist', false);
    $this->assertEquals('Does not exist', $res, 'licenseLong should return string passed in of match is not found');
  }

  public function testLicenseLink()
  {
    $res = $this->utility->licenseLink('CC BY', false);
    $this->assertEquals('http://creativecommons.org/licenses/by/3.0', $res, 'CC BY license not properly retrieved');

    $res = $this->utility->licenseLink('Does not exist', false);
    $this->assertEquals('', $res, 'licenseLink should return string passed in of match is not found');
  }

  public function testPermissionAsText()
  {
    $res = $this->utility->permissionAsText(0, false);
    $this->assertEquals('private', $res, '0 permission should be private');

    $res = $this->utility->permissionAsText(1, false);
    $this->assertEquals('public', $res, '1 permission should be public');

    $res = $this->utility->permissionAsText('foobar', false);
    $this->assertEquals('public', $res, 'foobar permission should be public');
  }

  public function testPlural()
  {
    $res = $this->utility->plural(0, 'word', false);
    $this->assertEquals('words', $res, 'plural for word with 0 incorrect');

    $res = $this->utility->plural(1, 'word', false);
    $this->assertEquals('word', $res, 'plural for word with 1 incorrect');

    $res = $this->utility->plural(2, 'word', false);
    $this->assertEquals('words', $res, 'plural for word with 2 incorrect');
  }

  public function testSelectPlural()
  {
    $res = $this->utility->selectPlural(0, 'was', 'were', false);
    $this->assertEquals('were', $res, 'plural for word with 0 selected incorrectly');

    $res = $this->utility->selectPlural(1, 'was', 'were', false);
    $this->assertEquals('was', $res, 'plural for word with 1 selected incorrectly');

    $res = $this->utility->selectPlural(2, 'was', 'were', false);
    $this->assertEquals('were', $res, 'plural for word with 2 selected incorrectly');
  }

  public function testReturnValue()
  {
    $res = $this->utility->returnValue('foo', false);
    $this->assertEquals('foo', $res, 'returnValue when false should return');

    ob_start();
    $this->utility->returnValue('foo', true);
    $res = ob_get_contents();
    $this->assertEquals('foo', $res, 'returnValue when true should write');
    ob_end_clean();
  }

  public function testSafe()
  {
    $res = $this->utility->safe('Hello " there', false);
    $this->assertEquals('Hello &quot; there', $res, 'content not properly safed');
  }

  public function testSafeWithTags()
  {
    $res = $this->utility->safe('Hello " <a href="">there</a> <script>location.href="http://google.com";</script>', '<a>', false);
    $this->assertEquals('Hello " <a href="">there</a> location.href="http://google.com";', $res, 'content not properly safed with tags allowed');
  }

  // TODO populate this test
  public function testStaticMapUrl() {}

  public function testTimeAsText()
  {
    date_default_timezone_set('America/Los_Angeles');
    $res = $this->utility->timeAsText('', null, null, false);
    $this->assertEquals('', $res, 'when no time is passed then return empty');

    $res = $this->utility->timeAsText(time()+3601, null, null, false);
    $this->assertEquals('--', $res, 'future hours returns --');

    $res = $this->utility->timeAsText(time()-3601, null, null, false);
    $this->assertEquals(' 1 hour ago ', $res, 'future hours returns --');

    $res = $this->utility->timeAsText(time()-7201, null, null, false);
    $this->assertEquals(' 2 hours ago ', $res, 'future hours returns --');

    $res = $this->utility->timeAsText(time()-86400, null, null, false);
    $this->assertEquals(' 1 day ago ', $res, 'future hours returns --');

    $res = $this->utility->timeAsText(time()-(86400*2), null, null, false);
    $this->assertEquals(' 2 days ago ', $res, 'future hours returns --');

    $res = $this->utility->timeAsText(time()-(86400*8), null, null, false);
    $this->assertEquals(' 1 week ago ', $res, 'future hours returns --');

    $res = $this->utility->timeAsText(time()-(86400*14), null, null, false);
    $this->assertEquals(' 2 weeks ago ', $res, 'future hours returns --');

    $res = $this->utility->timeAsText(time()-(86400*35), null, null, false);
    $this->assertEquals(' 1 month ago ', $res, 'future hours returns --');

    $res = $this->utility->timeAsText(time()-(86400*65), null, null, false);
    $this->assertEquals(' 2 months ago ', $res, 'future hours returns --');

    $res = $this->utility->timeAsText(time()-(86400*370), null, null, false);
    $this->assertEquals(' 1 year ago ', $res, 'future hours returns --');

    $res = $this->utility->timeAsText(time()-(86400*370*2), null, null, false);
    $this->assertEquals(' 2 years ago ', $res, 'future hours returns --');
  }
}
