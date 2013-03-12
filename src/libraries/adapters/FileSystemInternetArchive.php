<?php
/**
 * InternetArchive implementation for FileSystemInterface
 * Extends the S3 adapter
 *
 * This class defines the functionality defined by FileSystemInterface for DreamObjects.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemInternetArchive extends FileSystemS3 implements FileSystemInterface
{
  public function __construct($config = null, $params = null)
  {
    // temporary hack until we get key encryption working
    $this->config = !is_null($config) ? $config : getConfig()->get();
    $params['fs'] = new AmazonS3($this->config->credentials->awsKey, $this->config->credentials->awsSecret);

    parent::__construct($config, $params);
    $this->setHostname('s3.us.archive.org');
    $this->setSSL(false);
    $this->headers = array('x-archive-interactive-priority' => '1');
    $this->setUploadType(FileSystemS3::uploadTypeInline);
  }
}

