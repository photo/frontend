<?php
class FileSystemProviderS3 implements FileSystemInterface
{
  public function __construct($opts)
  {
    $this->fs = new AmazonS3($opts->awsKey, $opts->awsSecret);
  }

  public function putDirectory($directoryName)
  {
    return true;
  }

  public function putFile($localFile, $remoteFile)
  {
    $opts = func_get_args();
    $res = $this->fs->create_object(getConfig()->get('aws')->bucketName, $remoteFile, array('fileUpload' => $localFile));
    return $res->isOK();
  }
}
