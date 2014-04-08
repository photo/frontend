<?php
class MediaWrapper extends Media
{
  public function __construct()
  {
    parent::__construct();
  }

  protected function readExif($localFile, $allowAutoRotate)
  {
    return array('foo' => 'bar', 'allowAutoRotate' => $allowAutoRotate);
  }

  protected function readIptc($localFile)
  {
    return array('foo' => 'bar', 'empty' => '', 'tags' => array('one','two'));
  }

  public function setDateAttributes($attributes)
  {
    return parent::setDateAttributes($attributes);
  }

  public function setExifAttributes($attributes, $localFile, $mediaType)
  {
    return parent::setExifAttributes($attributes, $localFile, $mediaType);
  }

  public function setIptcAttributes($attributes, $localFile, $mediaType)
  {
    return parent::setIptcAttributes($attributes, $localFile, $mediaType);
  }

  public function setDateTagsByAttributes($attributes)
  {
    return parent::setDateTagsByAttributes($attributes);
  }

  public function setTagAttributes($attributes)
  {
    return parent::setTagAttributes($attributes);
  }

  public function setPathAttributes($attributes, $paths)
  {
    return parent::setPathAttributes($attributes, $paths);
  }

  public function setMediaSpecificAttributes($attributes, $localFile)
  {
    return parent::setMediaSpecificAttributes($attributes, $localFile);
  }

  public function trim($str)
  {
    return parent::trim($str);
  }

  public function whitelistAttributes($attributes)
  {
    return parent::whitelistAttributes($attributes);
  }

  public function isUploadedFile($localFile)
  {
    if(strstr($localFile, 'opme-via-url'))
      return parent::isUploadedFile($localFile);
    return true;
  }
}

class MediaTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->config = new stdClass;
    $this->config->site = new stdClass;
    $this->config->application = new stdClass;
    $this->config->application->appId = 'appId';
    $this->config->photos = new stdClass;
    $this->config->photos->autoTagWithDate = 1;
    $this->config->user = new stdClass;
    $this->config->user->email = 'email@example.com';

    $this->media = new MediaWrapper;
    $this->media->inject('owner', $this->config->user->email);
    $this->photo = sprintf('%s/helpers/files/landscape.jpg', dirname(dirname(dirname(__FILE__))));
    $this->video = sprintf('%s/helpers/files/video.mp4', dirname(dirname(dirname(__FILE__))));
    $this->text = sprintf('%s/helpers/files/file.txt', dirname(dirname(dirname(__FILE__))));
  }

  public function testGetMediaTypePhoto()
  {
    $type = $this->media->getMediaType($this->photo);
    $this->assertEquals(Media::typePhoto, $type);
  }

  public function testGetMediaTypeVideo()
  {
    $type = $this->media->getMediaType($this->video);
    $this->assertEquals(Media::typeVideo, $type);
  }

  public function testGetMediaTypeText()
  {
    $type = $this->media->getMediaType($this->text);
    $this->assertFalse($type);
  }

  public function testIsValidMimeTypePhoto()
  {
    $res = $this->media->isValidMimeType($this->photo);
    $this->assertTrue($res);
  }

  public function testIsValidMimeTypeVideo()
  {
    $res = $this->media->isValidMimeType($this->video);
    $this->assertTrue($res);
  }

  public function testIsValidMimeTypeText()
  {
    $res = $this->media->isValidMimeType($this->text);
    $this->assertFalse($res);
  }

  public function testIsUploadedFileAsUrl()
  {
    $res = $this->media->isUploadedFile('opme-via-url-foobar');
    $this->assertTrue($res);
  }

  public function testPrepareAttributesWithOriginalFilenameEmpty()
  {
    $fs = $this->getMock('fs', array('getMetaData','getHost'));
    $fs->expects($this->any())
      ->method('getMetaData')
      ->will($this->returnValue(null));
    $fs->expects($this->any())
      ->method('getHost')
      ->will($this->returnValue('fshost'));
    $this->media->inject('fs', $fs);
    $this->media->inject('config', $this->config);

    $attr = array('tags' => '1234', 'filenameOriginal' => '');
    $res = $this->media->prepareAttributes($attr, $this->photo, 'passedin');
    $this->assertEquals('passedin', $res['filenameOriginal']);
  }

  public function testPrepareAttributesWithOriginalFilenameOverride()
  {
    $fs = $this->getMock('fs', array('getMetaData','getHost'));
    $fs->expects($this->any())
      ->method('getMetaData')
      ->will($this->returnValue(null));
    $fs->expects($this->any())
      ->method('getHost')
      ->will($this->returnValue('fshost'));
    $this->media->inject('fs', $fs);
    $this->media->inject('config', $this->config);

    $attr = array('tags' => '1234', 'filenameOriginal' => 'attribute');
    $res = $this->media->prepareAttributes($attr, $this->photo, 'passedin');
    $this->assertEquals('attribute', $res['filenameOriginal']);
  }

  public function testPrepareAttributesWithFSMetaDataNull()
  {
    $fs = $this->getMock('fs', array('getMetaData','getHost'));
    $fs->expects($this->any())
      ->method('getMetaData')
      ->will($this->returnValue(null));
    $fs->expects($this->any())
      ->method('getHost')
      ->will($this->returnValue('fshost'));
    $this->media->inject('fs', $fs);
    $this->media->inject('config', $this->config);

    $attr = array('tags' => '1234');
    $res = $this->media->prepareAttributes($attr, $this->photo, 'foo');
    $this->assertTrue(!isset($res['extraFileSystem']));
  }

  public function testPrepareAttributesForcedParams()
  {
    $fs = $this->getMock('fs', array('getMetaData','getHost'));
    $fs->expects($this->any())
      ->method('getMetaData')
      ->will($this->returnValue(null));
    $fs->expects($this->any())
      ->method('getHost')
      ->will($this->returnValue('fshost'));
    $this->media->inject('fs', $fs);
    $this->media->inject('config', $this->config);

    $attr = array('tags' => '1234');
    $res = $this->media->prepareAttributes($attr, $this->photo, 'foo');
    $this->assertEquals('email@example.com', $res['owner'], 'Failed checking owner');
    //$this->assertEquals('email@example.com', $res['actor'], 'Failed checking actor');
    $this->assertEquals(2249, $res['size'], 'Failed checking size');
  }

  public function testRequireDefaults()
  {
    $fs = $this->getMock('fs', array('getMetaData','getHost'));
    $fs->expects($this->any())
      ->method('getHost')
      ->will($this->returnValue('fshost'));
    $this->media->inject('fs', $fs);
    $this->media->inject('config', $this->config);
    $res = $this->media->requireDefaults(array('foo' => 'bar'));
    $this->assertEquals('bar', $res['foo'], 'Make sure passed in param is present');
    $this->assertEquals('appId', $this->config->application->appId, 'Confirm appId is added');
  }

  public function testRequireDefaultsOverride()
  {
    $fs = $this->getMock('fs', array('getMetaData','getHost'));
    $fs->expects($this->any())
      ->method('getHost')
      ->will($this->returnValue('fshost'));
    $this->media->inject('fs', $fs);
    $this->media->inject('config', $this->config);
    $res = $this->media->requireDefaults(array('appId' => 'bar'));
    $this->assertEquals('bar', $res['appId'], 'Confirm appId is overridden');
  }

  public function testSetDateAttributesPreservesExistingParameters()
  {
    $res = $this->media->setDateAttributes(array('foo' => 'bar'));
    $this->assertTrue(isset($res['foo']), 'foo should be preserved');
  }

  public function testSetDateAttributesWithNoDateTaken()
  {
    $res = $this->media->setDateAttributes(array());
    $this->assertTrue(isset($res['dateTaken']), 'dateTaken should be set if not passed in');
    $this->assertTrue($res['dateTaken'] > (time()-30), 'dateTaken should be set if not passed in');
  }

  public function testSetDateAttributesWithPriorDateTaken()
  {
    $res = $this->media->setDateAttributes(array('dateTaken'=>1293304870));
    $this->assertEquals(1293304870, $res['dateTaken'], 'dateTaken should be preserved');
    $this->assertEquals(12, $res['dateTakenMonth'], 'dateTaken should be preserved');
  }

  public function testSetDateAttributesWithNoDateUploaded()
  {
    $res = $this->media->setDateAttributes(array());
    $this->assertTrue(isset($res['dateUploaded']), 'dateUploaded should be set if not passed in');
    $this->assertTrue($res['dateUploaded'] > (time()-30), 'dateUploaded should be set if not passed in');
  }

  public function testSetDateAttributesWithPriorDateUploaded()
  {
    $res = $this->media->setDateAttributes(array('dateUploaded'=>1293304870));
    $this->assertEquals(1293304870, $res['dateUploaded'], 'dateUploaded should be preserved');
    $this->assertEquals(12, $res['dateUploadedMonth'], 'dateUploaded should be preserved');
  }

  public function testSetExifPreservesExistingParameters()
  {
    $res = $this->media->setExifAttributes(array('foo' => 'bar'), $this->photo, Media::typePhoto);
    $this->assertTrue(isset($res['foo']));
  }

  public function testSetExifUsesAutoRotate()
  {
    $res = $this->media->setExifAttributes(array('allowAutoRotate' => '0'), $this->photo, Media::typePhoto);
    $this->assertEquals('0', $res['allowAutoRotate']);
  }

  public function testSetIptcPreservesExistingParameters()
  {
    $res = $this->media->setIptcAttributes(array('foo' => 'bar'), $this->photo, Media::typePhoto);
    $this->assertTrue(isset($res['foo']), 'foo should be preserved');
  }

  public function testSetIptcMergesTags()
  {
    $res = $this->media->setIptcAttributes(array('tags' => 'zero,infinite'), $this->photo, Media::typePhoto);
    $this->assertTrue(!isset($res['empty']));
  }

  public function testSetIptcSkipEmpty()
  {
    $res = $this->media->setIptcAttributes(array('tags' => 'zero,infinite'), $this->photo, Media::typePhoto);
    $this->assertEquals('zero,infinite,one,two', $res['tags'], 'tags should be merged');
  }

  public function testSetDateTagsByAttributesAutoTagWithDateNo()
  {
    $this->config->photos->autoTagWithDate = 0;
    $this->media->inject('config', $this->config);
    $res = $this->media->setDateTagsByAttributes(array('dateTaken' => '1293304870'));
    $this->assertFalse(isset($res['tags']));
  }

  public function testSetTagAttributesAutoTagWithDateYes()
  {
    $this->media->inject('config', $this->config);
    $res = $this->media->setTagAttributes(array('dateTaken' => '1293304870'));
    $this->assertTrue(strstr($res['tags'], 'December') !== false, 'Month tags');
    $this->assertTrue(strstr($res['tags'], '2010') !== false, 'Year Tags');
    // preserve and add
    $res = $this->media->setTagAttributes(array('dateTaken' => '1293304870','tags'=>'one'));
    $this->assertTrue(strstr($res['tags'], 'one') !== false, 'one tag');
    $this->assertTrue(strstr($res['tags'], '2010') !== false, 'Year Tags');
  }

  public function testSetTagAttributesAutoTagWithDateNo()
  {
    $this->config->photos->autoTagWithDate = 0;
    $this->media->inject('config', $this->config);
    $res = $this->media->setTagAttributes(array('dateTaken' => '1293304870'));
    $this->assertTrue(!isset($res['tags']), 'No tags should exist if not passed in');
    // preserve and skip
    $res = $this->media->setTagAttributes(array('dateTaken' => '1293304870','tags'=>'one'));
    $this->assertTrue(strstr($res['tags'], 'one') !== false, 'one tag');
    $this->assertTrue(strstr($res['tags'], '2010') === false, 'Year Tags');
  }

  public function testSetPaths()
  {
    $attrs = array('foo' => 'bar');
    $paths = array( 'pathOriginal' => 'original', 'pathBase' => 'base');
    $merged = array_merge($attrs, $paths);
    $res = $this->media->setPathAttributes($attrs, $paths);
    $this->assertEquals($merged, $res);
  }

  public function testSetPathsWithInvlidOverrideAttempt()
  {
    $attrs = array('foo' => 'bar', 'pathOriginal' => 'invalid');
    $paths = array( 'pathOriginal' => 'original', 'pathBase' => 'base');
    $merged = array_merge($attrs, $paths);
    $res = $this->media->setPathAttributes($attrs, $paths);
    $this->assertEquals($merged, $res);
  }

  public function testSetMediaSpecificAttributesForVideo()
  {
    $attrs = array('foo' => 'bar', 'pathOriginal' => 'invalid');
    $res = $this->media->setMediaSpecificAttributes($attrs, $this->video);
    $this->assertTrue($res['video'], $res);
  }

  public function testSetDateTagsByAttributesNoAutoTag()
  {
    $this->config->photos->autoTagWithDate = 0;
    $this->media->inject('config', $this->config);

    $attrs = array('foo' => 'bar', 'dateTaken' => '1293304870', 'tags' => 'one,two,three');
    $res = $this->media->SetDateTagsByAttributes($attrs);
    $this->assertEquals($attrs, $res, 'When autoTagWithDate is 0 then the attributes should not be altered when tags exist'); 


    $attrs = array('foo' => 'bar', 'dateTaken' => '1293304870');
    $res = $this->media->SetDateTagsByAttributes($attrs);
    $this->assertEquals($attrs, $res, 'When autoTagWithDate is 0 then the attributes should not be altered even if no tags exist'); 
  }

  public function testSetDateTagsByAttributesYesAutoTag()
  {
    $this->media->inject('config', $this->config);

    $attrs = array('foo' => 'bar', 'dateTaken' => '1293304870', 'tags' => 'one,two,three');
    $res = $this->media->SetDateTagsByAttributes($attrs);
    $attrs['tags'] .= ',December,2010';
    $this->assertEquals($attrs, $res, 'Date tags should be appended to list'); 

    $attrs = array('foo' => 'bar', 'dateTaken' => '1293304870');
    $res = $this->media->SetDateTagsByAttributes($attrs);
    $attrs['tags'] = 'December,2010';
    $this->assertEquals($attrs, $res, 'When no tags exist the tags attribute should be created'); 
  }

  public function testWhitelistAttributes()
  {
    $res = $this->media->whitelistAttributes(array('invalid' => 'invalid', 'dateTaken' => '1293304870','tags'=>'one'));
    $this->assertTrue(isset($res['dateTaken']), 'dateTaken is valid');
    $this->assertTrue(!isset($res['invalid']), 'invalid is not valid');
  }

  public function testTrim()
  {
    // starts
    $this->assertEquals("\tabcd", $this->media->trim("\tabcd"), 'Starts with tab fails');
    $this->assertEquals("\t abcd", $this->media->trim("\t abcd"), 'Starts with tab then space fails');
    $this->assertEquals("\tabcd", $this->media->trim(" \tabcd"), 'Starts with space then tab fails');
    $this->assertEquals("abcd", $this->media->trim("\nabcd"), 'Starts with new line fails');
    $this->assertEquals("\tabcd", $this->media->trim("\n\tabcd"), 'Starts with new line then tab fails');

    // ends
    $this->assertEquals("abcd", $this->media->trim("abcd\t"), 'Ends with tab fails');
    $this->assertEquals("abcd", $this->media->trim("abcd\t "), 'Ends with tab then space fails');
    $this->assertEquals("abcd", $this->media->trim("abcd \t"), 'Ends with space then tab fails');
    $this->assertEquals("abcd", $this->media->trim("abcd\n"), 'Ends with new line fails');
    $this->assertEquals("abcd", $this->media->trim("abcd\n\t"), 'Ends with new line then tab fails');

    // starts and ends
    $this->assertEquals("\tabcd", $this->media->trim("\tabcd\t"), 'Starts/Ends with tab fails');
    $this->assertEquals("\t abcd", $this->media->trim("\t abcd\t "), 'Starts/Ends with tab then space fails');
    $this->assertEquals("\tabcd", $this->media->trim(" \tabcd \t"), 'Starts/Ends with space then tab fails');
    $this->assertEquals("abcd", $this->media->trim("\nabcd\n"), 'Starts/Ends with new line fails');
    $this->assertEquals("\tabcd", $this->media->trim("\n\tabcd\n\t"), 'Starts/Ends with new line then tab fails');
    $this->assertEquals(array(1), $this->media->trim(array(1)), 'Trim should not convert array to string');
  }
}
