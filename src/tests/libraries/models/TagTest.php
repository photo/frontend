<?php
class TagTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    // to test the write methods
    $this->tag = new Tag();
    //$this->tag->config = json_decode(json_encode(array('application' => array('appId' => 'foo'), 'user' => array('email' => 'bar'))));
  }

  public function testDeleteSuccess()
  {
    $db = $this->getMock('Db', array('deleteTag'));
    $db->expects($this->any())
      ->method('deleteTag')
      ->will($this->returnValue(true));
    $this->tag->inject('db', $db);
    $res = $this->tag->delete('a');
    $this->assertTrue($res);
  }

  public function testDeleteFailure()
  {
    $db = $this->getMock('Db', array('deleteTag'));
    $db->expects($this->any())
      ->method('deleteTag')
      ->will($this->returnValue(false));
    $this->tag->inject('db', $db);
    $res = $this->tag->delete('a');
    $this->assertFalse($res);
  }

  public function testGetTagUpdateCountValuesBasic()
  {
    $tags = array(
      array('id' => 'one', 'countPublic' => 1, 'countPrivate' => 1),
      array('id' => 'two', 'countPublic' => 2, 'countPrivate' => 2),
      array('id' => 'three', 'countPublic' => 3, 'countPrivate' => 3),
      array('id' => 'four', 'countPublic' => 4, 'countPrivate' => 4),
    );

    $db = $this->getMock('Db', array('getTags'));
    $db->expects($this->any())
      ->method('getTags')
      ->will($this->returnValue($tags));
    $this->tag->inject('db', $db);
    $res = $this->tag->getUpdateTagCountValues(array('one'), array('two'), 1, 1);
    $expected = array(
      array('id' => 'one', 'countPublic' => 0, 'countPrivate' => 0),
      array('id' => 'two', 'countPublic' => 3, 'countPrivate' => 3)
    );
    $this->assertEquals($expected, $res);
  }

  public function testGetTagUpdateCountValuesNewTag()
  {
    $tags = array(
      array('id' => 'one', 'countPublic' => 1, 'countPrivate' => 1),
      array('id' => 'two', 'countPublic' => 2, 'countPrivate' => 2),
      array('id' => 'three', 'countPublic' => 3, 'countPrivate' => 3),
      array('id' => 'four', 'countPublic' => 4, 'countPrivate' => 4),
    );

    $db = $this->getMock('Db', array('getTags'));
    $db->expects($this->any())
      ->method('getTags')
      ->will($this->returnValue($tags));
    $this->tag->inject('db', $db);
    $res = $this->tag->getUpdateTagCountValues(array(), array('five'), 1, 1);
    $expected = array(
      array('id' => 'five', 'countPublic' => 1, 'countPrivate' => 1)
    );
    $this->assertEquals($expected, $res);
  }

  public function testGetTagUpdateCountValuesDeleteTag()
  {
    $tags = array(
      array('id' => 'one', 'countPublic' => 1, 'countPrivate' => 1),
      array('id' => 'two', 'countPublic' => 2, 'countPrivate' => 2),
      array('id' => 'three', 'countPublic' => 3, 'countPrivate' => 3),
      array('id' => 'four', 'countPublic' => 4, 'countPrivate' => 4),
    );

    $db = $this->getMock('Db', array('getTags'));
    $db->expects($this->any())
      ->method('getTags')
      ->will($this->returnValue($tags));
    $this->tag->inject('db', $db);
    $res = $this->tag->getUpdateTagCountValues(array('one','two','three','four'), array('two', 'three', 'four'), 1, 1);
    $expected = array(
      array('id' => 'one', 'countPublic' => 0, 'countPrivate' => 0)
    );
    $this->assertEquals($expected[0], $res[0]);
  }

  public function testGetTagUpdateCountValuesPermissionMakePrivate()
  {
    $tags = array(
      array('id' => 'one', 'countPublic' => 1, 'countPrivate' => 1),
      array('id' => 'two', 'countPublic' => 2, 'countPrivate' => 2)
    );

    $db = $this->getMock('Db', array('getTags'));
    $db->expects($this->any())
      ->method('getTags')
      ->will($this->returnValue($tags));
    $this->tag->inject('db', $db);
    $res = $this->tag->getUpdateTagCountValues(array('one','two'), array('one', 'two'), 0, 1);
    $expected = array(
      array('id' => 'one', 'countPublic' => 0, 'countPrivate' => 1),
      array('id' => 'two', 'countPublic' => 1, 'countPrivate' => 2)
    );
    $this->assertEquals($expected, $res);
  }

  public function testGetTagUpdateCountValuesPermissionMakePublic()
  {
    $tags = array(
      array('id' => 'one', 'countPublic' => 0, 'countPrivate' => 1),
      array('id' => 'two', 'countPublic' => 1, 'countPrivate' => 2)
    );

    $db = $this->getMock('Db', array('getTags'));
    $db->expects($this->any())
      ->method('getTags')
      ->will($this->returnValue($tags));
    $this->tag->inject('db', $db);
    $res = $this->tag->getUpdateTagCountValues(array('one','two'), array('one', 'two'), 1, 0);
    $expected = array(
      array('id' => 'one', 'countPublic' => 1, 'countPrivate' => 1),
      array('id' => 'two', 'countPublic' => 2, 'countPrivate' => 2)
    );
    $this->assertEquals($expected, $res);
  }

  public function testUpdateTagCountsSuccess()
  {
    $db = $this->getMock('Db', array('getTags','postTags'));
    $db->expects($this->any())
      ->method('getTags')
      ->will($this->returnValue(array()));
    $db->expects($this->any())
      ->method('postTags')
      ->will($this->returnValue(true));
    $this->tag->inject('db', $db);

    $res = $this->tag->updateTagCounts(array('one'), array('two'), 1, 1);
    $this->assertTrue($res);
  }

  public function testUpdateTagCountsFailure()
  {
    $db = $this->getMock('Db', array('getTags','postTags'));
    $db->expects($this->any())
      ->method('getTags')
      ->will($this->returnValue(array()));
    $db->expects($this->any())
      ->method('postTags')
      ->will($this->returnValue(false));
    $this->tag->inject('db', $db);

    $res = $this->tag->updateTagCounts(array('one'), array('two'), 1, 1);
    $this->assertFalse($res);
  }

  public function testGroupByWeight()
  {
    $tags = array(
      array('id' => 'one', 'count' => 1),
      array('id' => 'two', 'count' => 1),
      array('id' => 'three', 'count' => 3),
      array('id' => 'four', 'count' => 9),
    );
    $res = $this->tag->groupByWeight($tags);
    $this->assertEquals(1, $res[0]['weight']);
    $this->assertEquals(1, $res[1]['weight']);
    $this->assertEquals(3, $res[2]['weight']);
    $this->assertEquals(10, $res[3]['weight']);
  }

  public function testSanitize()
  {
    $res = $this->tag->sanitize('foo,bar');
    $this->assertEquals('foo-bar', $res);
    $res = $this->tag->sanitize('foo,bar,test');
    $this->assertEquals('foo-bar-test', $res);
  }

  public function testSanitizeTagsAsStringNoop()
  {
    $res = $this->tag->sanitizeTagsAsString('one,two');
    $this->assertEquals('one,two', $res);
  }

  public function testSanitizeTagsAsStringRemoveSpaces()
  {
    $res = $this->tag->sanitizeTagsAsString(' one , two ');
    $this->assertEquals('one,two', $res);
  }

  public function testSanitizeTagsAsStringSpacesWithCommas()
  {
    $res = $this->tag->sanitizeTagsAsString(' one , , two ');
    $this->assertEquals('one,two', $res);
  }

  public function testSanitizeTagsAsStringSpacesWithDuplicates()
  {
    $res = $this->tag->sanitizeTagsAsString('one,one,two,two');
    $this->assertEquals('one,two', $res);
  }

  /* Uncomment to verify issue Gh-399
   * public function testSanitizeTagsAsStringMixedCase()
  {
    $res = $this->tag->sanitizeTagsAsString('one,two,Two');
    $this->assertEquals('one,two', $res);

    $res = $this->tag->sanitizeTagsAsString('one,Two,two');
    $this->assertEquals('one,Two', $res);
  }*/

  public function testValidateParams()
  {
    $tagToValidate = array('countPrivate' => 2, 'countPublic' => 3, 'illegal' => 'badkey');
    $res = $this->tag->validateParams($tagToValidate);
    $expected = $tagToValidate;
    unset($expected['illegal']);
    $this->assertEquals($expected, $res);
  }
}
