<?php
/**
 * DreamHost DreamObjects implementation for FileSystemInterface
 * Extends the S3 adapter
 *
 * This class defines the functionality defined by FileSystemInterface for DreamObjects.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemDreamObjects extends FileSystemS3 implements FileSystemInterface
{
  public function __construct($config = null, $params = null)
  {
    parent::__construct($config, $params);
    $this->setHostname('objects.dreamhost.com');
    $this->setUploadType(FileSystemS3::uploadTypeInline);
  }
}
