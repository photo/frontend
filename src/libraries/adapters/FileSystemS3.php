<?php
class FileSystemS3 implements FileSystemInterface
{
  private $bucket;
  public function __construct($opts)
  {
    $this->fs = new AmazonS3($opts->awsKey, $opts->awsSecret);
    $this->bucket = getConfig()->get('aws')->s3BucketName;
  }

  public function deletePhoto($id)
  {
    $photo = getDb()->getPhoto($id);
    $queue = new CFBatchRequest();
    foreach($photo as $key => $value)
    {
      if(strncmp($key, 'path', 4) === 0)
        $this->fs->batch($queue)->delete_object($this->bucket, self::normalizePath($value));
    }
    $responses = $this->fs->batch($queue)->send();
    return $responses->areOK();
  }

  public function getPhoto($filename)
  {
    $filename = self::normalizePath($filename);
    $tmpname = '/tmp/'.uniqid('opme', true);
    $fp = fopen($tmpname, 'w+');
    $res = $this->fs->get_object($this->bucket, $filename, array('fileDownload' => $fp));
    fclose($fp);
    return $res->isOK() ? $tmpname : false;
  }

  public function putPhoto($localFile, $remoteFile, $acl = AmazonS3::ACL_PUBLIC)
  {
    $remoteFile = self::normalizePath($remoteFile);
    $opts = array('fileUpload' => $localFile, 'acl' => $acl, 'contentType' => 'image/jpeg');
    $res = $this->fs->create_object($this->bucket, $remoteFile, $opts);
    return $res->isOK();
  }

  public function putPhotos($files, $acl = AmazonS3::ACL_PUBLIC)
  {
    $queue = new CFBatchRequest();
    foreach($files as $file)
    {
      list($localFile, $remoteFile) = each($file);
      $opts = array('fileUpload' => $localFile, 'acl' => $acl, 'contentType' => 'image/jpeg'); 
      $remoteFile = self::normalizePath($remoteFile);
      $this->fs->batch($queue)->create_object($this->bucket, $remoteFile, $opts);
    }
    $responses = $this->fs->batch($queue)->send();
    return $responses->areOK();
  }

  public function getHost()
  {
    return getConfig()->get('aws')->s3Host;
  }

  public function initialize()
  {
    if(!$this->fs->validate_bucketname_create($this->bucket) || !$this->fs->validate_bucketname_support($this->bucket))
      return false;

    $res = $this->fs->create_bucket($this->bucket, AmazonS3::REGION_US_E1, AmazonS3::ACL_PUBLIC);
    return $res->isOK();
  }

  private function normalizePath($path)
  {
    return preg_replace('/^\/+/', '', $path);
  }
}
