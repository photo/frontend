<?php
/**
 * Archive.org adapter that extends much of the FileSystemLocal logic
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemArchiveOrgBase
{
  private $config, $parent;
  public $fs;
  public function __construct($parent, $config = null, $params = null)
  {
    $this->config = !is_null($config) ? $config : getConfig()->get();

    // here we remap the config so the FileSysetmS3 talks to archive.org
    $this->config->credentials->awsKey = $this->config->credentials->archiveOrgKey;
    $this->config->credentials->awsSecret = $this->config->credentials->archiveOrgSecret;
    $this->config->aws->s3BucketName = $this->config->archiveOrg->archiveOrgBucketName;

    $this->fs = new FileSystemS3($this->config);
    $this->fs->setHostname('s3.us.archive.org');
    $this->fs->setSSL(false);
    $this->fs->headers = array('x-archive-interactive-priority' => '1');
    $this->fs->setUploadType(FileSystemS3::uploadTypeInline);
  }

  public function getHost()
  {
    return $this->config->archiveOrg->archiveOrgHost;
  }

  public function segmentFiles($files)
  {
    foreach($files as $file)
    {
      list($localFile, $remoteFileArr) = each($file);
      $remoteFile = $remoteFileArr[0];
      if(strpos($remoteFile, '/original/') !== false)
        $myFiles[] = $file;
      else
        $parentFiles[] = $file;
    }
    return array('myFiles' => $myFiles, 'parentFiles' => $parentFiles);
  }
}
