<?php
class FileSystemProviderS3 implements FileSystemInterface
{
  private $bucket;
  public function __construct($opts)
  {
    $this->fs = new AmazonS3($opts->awsKey, $opts->awsSecret);
    $this->bucket = getConfig()->get('aws')->bucketName;
  }

  public function deletePhoto($id)
  {
    $photo = getDb()->getPhoto($id);
    foreach($photo as $key => $value)
    {
      
    }
    $this->fs->delete_object($this->bucket, $photoPath);
  }

  public function putPhoto($localFile, $remoteFile, $acl = AmazonS3::ACL_PUBLIC)
  {
    $remoteFile = preg_replace('/^\/+/', '', $remoteFile);
    $opts = array('fileUpload' => $localFile, 'acl' => $acl, 'contentType' => 'image/jpeg');
    return $this->fs->create_object($this->bucket, $remoteFile, $opts);
  }

  public function getHost()
  {
    return getConfig()->get('aws')->s3Host;
  }
}
