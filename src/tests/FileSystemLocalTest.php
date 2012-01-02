<?php
require_once './helpers/init.php';
require_once 'vfsStream/vfsStream.php';
require_once '../libraries/adapters/FileSystem.php';
require_once '../libraries/adapters/FileSystemLocal.php';

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
  public function setUp()
  {
    vfsStreamWrapper::register();
    vfsStreamWrapper::setRoot(new vfsStreamDirectory('testDir'));
    $this->root = vfsStream::url('testDir');
    $this->host = 'http://test';
    $config = array(
      'localfs' => array('fsRoot' => $this->root, 'fsHost' => $this->host),
      'paths' => array('temp' => sys_get_temp_dir())
    );
    $config = arrayToObject($config);
    $params = array('db' => true);
    $this->fs = new FileSystemLocal($config, $params);
    $this->file = 'file.jpg';
    $this->photo = array('id' => 'foo', 'path10x10' => "/{$this->file}");
    $this->vfsPath = sprintf('%s%s', $this->root, $this->photo['path10x10']);

    $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild($this->file), 'Init validation that vfs file does not exist failed');
  }

  public function testValidateVfsFunctionsSuccess()
  {
    file_put_contents($this->vfsPath, 'foo');
    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild($this->file), 'Post init validation that vfs file exists failed');
    unlink($this->vfsPath);
    $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild($this->file), 'Validating that unlink works on vfs failed');
  }
  public function testDeletePhotoSuccess()
  {
    file_put_contents($this->vfsPath, 'foo');
    // now check if the virtual fs has the file
    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild($this->file), 'Post init validation that vfs file exists failed');
    $res = $this->fs->deletePhoto($this->photo);
    $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild($this->file));
    $this->assertTrue($res, 'Delete photo did not return TRUE');
  }

  public function testDeletePhotoDoesNotExistSuccess()
  {
    $res = $this->fs->deletePhoto($this->photo);
    $this->assertTrue($res, 'Delete photo did not return TRUE even if photo does not exist');
  }

  public function testDeletePhotoFailure()
  {
    // Not quite sure how to write this test
  }

  public function testGetPhotoSuccess()
  {
    file_put_contents($this->vfsPath, 'foo');
    $file = $this->fs->getPhoto($this->photo['path10x10']);
    $this->assertFileExists($file, 'getPhoto did not create and return a valid file path');
    @unlink($file);
  }

  public function testGetPhotoFailure()
  {
    $file = $this->fs->getPhoto($this->photo['path10x10']);
    $this->assertFalse($file, 'getPhoto when file does not exist did not return FALSE');
  }

  public function testPutPhotoSuccess()
  {
    file_put_contents($this->vfsPath, 'foo');
    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild($this->file), 'Post init validation that vfs file exists failed');
    $copiedFileName = str_replace('.jpg', '-copy.jpg', basename($this->vfsPath));
    $copiedFileFullPath = str_replace(basename($this->vfsPath), $copiedFileName, $this->vfsPath);
    $res = $this->fs->putPhoto($this->vfsPath, "/{$copiedFileName}");
    $this->assertTrue($res, 'The putPhoto call did not return TRUE');
    $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild($copiedFileName), 'The copied file does not actually exist');
  }

  public function testPutPhotoDoesNotExistFailure()
  {
    $copiedFileName = str_replace('.jpg', '-copy.jpg', basename($this->vfsPath));
    $copiedFileFullPath = str_replace(basename($this->vfsPath), $copiedFileName, $this->vfsPath);
    $res = $this->fs->putPhoto($this->vfsPath, "/{$copiedFileName}");
    $this->assertFalse($res, 'The putPhoto call did not return FALSE');
  }

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
      array($this->vfsPath => '/'.$copiedFileName),
      array($secondFileFullPath => '/'.$copiedSecondFileName),
    );
    $res = $this->fs->putPhotos($files);

    $this->assertTrue($res, 'Putting multiple photos failed');
    $this->assertFileExists($copiedFileFullPath, 'Putting multiple photos (1 or 2) failed');
    $this->assertFileExists($copiedSecondFileFullPath, 'Putting multiple photos (2 or 2) failed');
  }

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

  public function testGetHost()
  {
    $host = $this->fs->getHost();
    $this->assertEquals($host, $this->host, 'getHost did not return the correct host');
  }
}
