<?php
/**
 * Archive.org implementation for FileSystemInterface
 * Extends the S3 adapter
 *
 * This class defines the functionality defined by FileSystemInterface for DreamObjects.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemArchiveOrg extends FileSystemS3 implements FileSystemInterface
{
  public function __construct($config = null, $params = null)
  {
    parent::__construct($config, $params);
    $this->setHostname('s3.us.archive.org');
    $this->setSSL(false);
    $this->headers = array('x-archive-interactive-priority' => '1');
    $this->setUploadType(FileSystemS3::uploadTypeInline);
  }
}

