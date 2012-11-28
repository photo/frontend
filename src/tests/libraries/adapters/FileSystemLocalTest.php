<?php
/*class FileSystemS3Override extends FileSystemS3
{
  public function __construct($config = null, $params = null)
  {
    parent::__construct($config, $params);
  }

  public function getBatchRequest()
  {
    return null;
  }
}*/

class FileSystemLocalTest extends PHPUnit_Framework_TestCase
{
  private $root = '';
  public function setUp()
  {
    $this->file = 'file.jpg';
    $this->photo = array('id' => 'foo', 'path10x10' => "/{$this->file}");

    if(class_exists('vfsStream'))
    {
      vfsStreamWrapper::register();
      vfsStreamWrapper::setRoot(new vfsStreamDirectory('fsDir'));
      $this->root = vfsStream::url('fsDir');
      $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild($this->file), 'Init validation that vfs file does not exist failed');
    }
    $this->vfsPath = sprintf('%s%s', $this->root, $this->photo['path10x10']);
    $this->host = 'http://test';
    $config = array(
      'localfs' => array('fsRoot' => $this->root, 'fsHost' => $this->host),
      'paths' => array('temp' => sys_get_temp_dir())
    );
    $config = arrayToObject($config);
    $params = array('db' => true);
    $this->fs = new FileSystemLocal($config, $params);

  }

  public function testValidateVfsFunctionSuccess()
  {
    if(!class_exists('vfsStream'))
    {
      $this->fail('The vfsStream package was not found. Skipping tests in FileSysemLocalTest. Install using `sudo pear channel-discover pear.bovigo.org && sudo pear install bovigo/vfsStream-beta`');
      return false;
    }

    file_put_contents($this->vfsPath, 'foo');
    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild($this->file), 'Post init validation that vfs file exists failed');
    unlink($this->vfsPath);
    $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild($this->file), 'Validating that unlink works on vfs failed');
    return true;
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testDeletePhotoSuccess()
  {
    file_put_contents($this->vfsPath, 'foo');
    // now check if the virtual fs has the file
    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild($this->file), 'Post init validation that vfs file exists failed');
    $res = $this->fs->deletePhoto($this->photo);
    $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild($this->file));
    $this->assertTrue($res, 'Delete photo did not return TRUE');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testDeletePhotoDoesNotExistSuccess()
  {
    $res = $this->fs->deletePhoto($this->photo);
    $this->assertTrue($res, 'Delete photo did not return TRUE even if photo does not exist');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testDeletePhotoFailure()
  {
    // Not quite sure how to write this test
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testGetPhotoSuccess()
  {
    file_put_contents($this->vfsPath, 'foo');
    $file = $this->fs->getPhoto($this->photo['path10x10']);
    $this->assertFileExists($file, 'getPhoto did not create and return a valid file path');
    @unlink($file);
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testGetPhotoFailure()
  {
    $file = $this->fs->getPhoto($this->photo['path10x10']);
    $this->assertFalse($file, 'getPhoto when file does not exist did not return FALSE');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testPutPhotoSuccess()
  {
    file_put_contents($this->vfsPath, 'foo');
    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild($this->file), 'Post init validation that vfs file exists failed');
    $copiedFileName = str_replace('.jpg', '-copy.jpg', basename($this->vfsPath));
    $copiedFileFullPath = str_replace(basename($this->vfsPath), $copiedFileName, $this->vfsPath);
    $res = $this->fs->putPhoto($this->vfsPath, "/{$copiedFileName}", 1234);
    $this->assertTrue($res, 'The putPhoto call did not return TRUE');
    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild($copiedFileName), 'The copied file does not actually exist');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testPutPhotoDoesNotExistFailure()
  {
    $copiedFileName = str_replace('.jpg', '-copy.jpg', basename($this->vfsPath));
    $copiedFileFullPath = str_replace(basename($this->vfsPath), $copiedFileName, $this->vfsPath);
    $res = $this->fs->putPhoto($this->vfsPath, "/{$copiedFileName}", 1234);
    $this->assertFalse($res, 'The putPhoto call did not return FALSE');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testPutPhotosSuccess()
  {
    file_put_contents($this->vfsPath, 'foo');
    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild($this->file), 'Post init validation that vfs file exists failed');

    $copiedFileName = str_replace('.jpg', '-copy.jpg', basename($this->vfsPath));
    $copiedFileFullPath = str_replace(basename($this->vfsPath), $copiedFileName, $this->vfsPath);

    $secondFileName = str_replace('.jpg', '-second.jpg', basename($this->vfsPath));
    $secondFileFullPath = str_replace(basename($this->vfsPath), $secondFileName, $this->vfsPath);
    file_put_contents($secondFileFullPath, 'foo');

    $copiedSecondFileName = str_replace('.jpg', '-copy.jpg', basename($secondFileFullPath));
    $copiedSecondFileFullPath = str_replace(basename($secondFileFullPath), $copiedFileName, $secondFileFullPath);
    

    $files = array(
      array($this->vfsPath => array('/'.$copiedFileName, 1234)),
      array($secondFileFullPath => array('/'.$copiedSecondFileName, 1234)),
    );
    $res = $this->fs->putPhotos($files);

    $this->assertTrue($res, 'Putting multiple photos failed');
    $this->assertFileExists($copiedFileFullPath, 'Putting multiple photos (1 or 2) failed');
    $this->assertFileExists($copiedSecondFileFullPath, 'Putting multiple photos (2 or 2) failed');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testPutPhotosWithDateTakenSuccess()
  {
    // Gh-1012
    file_put_contents($this->vfsPath, 'foo');
    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild($this->file), 'Post init validation that vfs file exists failed');

    $copiedFileName = str_replace('.jpg', '-copy.jpg', basename($this->vfsPath));
    $copiedFileFullPath = str_replace(basename($this->vfsPath), $copiedFileName, $this->vfsPath);
    
    $files = array(
      array($this->vfsPath => array('/' . date('Ym', strtotime('1/1/2000')) . '/'.$copiedFileName, strtotime('1/1/2000'))),
    );
    $res = $this->fs->putPhotos($files);
    $this->assertTrue($res, 'Putting multiple photos failed');
    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild(sprintf('%s/%s', date('Ym', strtotime('1/1/2000')), $copiedFileName)), 'File does not exist at specified dateTaken');
  }


  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testPutPhotosFailure()
  {
    $copiedFileName = str_replace('.jpg', '-copy.jpg', basename($this->vfsPath));
    $copiedFileFullPath = str_replace(basename($this->vfsPath), $copiedFileName, $this->vfsPath);

    $files = array(
      array($this->vfsPath => '/'.$copiedFileName)
    );
    $res = $this->fs->putPhotos($files);
    $this->assertFalse($res, 'The putPhotos call did not return FALSE when the source file DNE');
    $this->assertFileNotExists($copiedFileFullPath, 'The putPhotos which returned FALSE magically put a photo on disk');
  }

  /**
   * @depends testValidateVfsFunctionSuccess
   */
  public function testGetHost()
  {
    $host = $this->fs->getHost();
    $this->assertEquals($host, $this->host, 'getHost did not return the correct host');
  }
}
