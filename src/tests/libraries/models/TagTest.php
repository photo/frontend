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

  public function testRemoveEmptyTags()
  {
    $db = $this->getMock('Db', array('getTags'));
    $tags = array(
      array('id' => '', 'count' => 1)
    );
    $db->expects($this->any())
      ->method('getTags')
      ->will($this->returnValue(array()));
    $this->tag->inject('db', $db);
    $res = $this->tag->getTags();
    $this->assertEquals(array(), $res, 'Empty tags should be removed');
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
