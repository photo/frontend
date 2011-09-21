<?php
/**
 * Amazon AWS S3 implementation for FileSystemInterface
 *
 * This class defines the functionality defined by FileSystemInterface for AWS S3.
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FileSystemS3 implements FileSystemInterface
{
  /**
    * Member variables holding the names to the bucket and the file system object itself.
    * @access private
    * @var array
    */
  private $bucket, $fs;

  /**
    * Constructor
    *
    * @return void
    */
  public function __construct()
  {
    $this->fs = new AmazonS3(getConfig()->get('credentials')->awsKey, getConfig()->get('credentials')->awsSecret);
    $this->bucket = getConfig()->get('aws')->s3BucketName;
  }

  /**
    * Deletes a photo (and all generated versions) from the file system.
    * To get a list of all the files to delete we first have to query the database and find out what versions exist.
    *
    * @param string $id ID of the photo to delete
    * @return boolean
    */
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

  /**
    * Retrieves a photo from the remote file system as specified by $filename.
    * This file is stored locally and the path to the local file is returned.
    *
    * @param string $filename File name on the remote file system.
    * @return mixed String on success, FALSE on failure.
    */
  public function getPhoto($filename)
  {
    $filename = self::normalizePath($filename);
    $tmpname = '/tmp/'.uniqid('opme', true);
    $fp = fopen($tmpname, 'w+');
    $res = $this->fs->get_object($this->bucket, $filename, array('fileDownload' => $fp));
    fclose($fp);
    return $res->isOK() ? $tmpname : false;
  }

  /**
    * Allows injection of member variables.
    * Primarily used for unit testing with mock objects.
    *
    * @param string $name Name of the member variable
    * @param mixed $value Value of the member variable
    * @return void
    */
  public function inject($name, $value)
  {
    $this->$name = $value;
  }

  /**
    * Writes/uploads a new photo to the remote file system.
    *
    * @param string $localFile File name on the local file system.
    * @param string $remoteFile File name to be saved on the remote file system.
    * @param string $acl Permission setting for this photo.
    * @return boolean
    */
  public function putPhoto($localFile, $remoteFile, $acl = AmazonS3::ACL_PUBLIC)
  {
    $remoteFile = self::normalizePath($remoteFile);
    $opts = array('fileUpload' => $localFile, 'acl' => $acl, 'contentType' => 'image/jpeg');
    $res = $this->fs->create_object($this->bucket, $remoteFile, $opts);
    if(!$res->isOK())
      getLogger()->crit('Could not put photo on the file system: ' . var_export($res));
    return $res->isOK();
  }

  /**
    * Writes/uploads new photos in bulk and in parallel to the remote file system.
    *
    * @param array $files Array where each row represents a file with the key being the local file name and the value being the remote.
    *   [{"/path/to/local/file.jpg": "/path/to/save/on/remote.jpg"}...]
    * @param string $remoteFile File name to be saved on the remote file system.
    * @param string $acl Permission setting for this photo.
    * @return boolean
    */
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
    if(!$responses->areOK())
    {
      foreach($responses as $resp)
        getLogger()->crit(var_export($resp, 1));
    }
    return $responses->areOK();
  }

  /**
    * Get the hostname for the remote filesystem to be used in constructing public URLs.
    * @return string
    */
  public function getHost()
  {
    return getConfig()->get('aws')->s3Host;
  }

  /**
    * Initialize the remote file system by creating buckets and setting permissions and settings.
    * This is called from the Setup controller.
    * @return boolean
    */
  public function initialize()
  {
    getLogger()->info('Initializing file system');
    if(!$this->fs->validate_bucketname_create($this->bucket) || !$this->fs->validate_bucketname_support($this->bucket))
    {
      getLogger()->warn("The bucket name you provided ({$this->bucket}) is invalid.");
      return false;
    }

    $buckets = $this->fs->get_bucket_list("/^{$this->bucket}$/");
    if(count($buckets) == 0)
    {
      getLogger()->info("Bucket {$this->bucket} does not exist, creating it now");
      $res = $this->fs->create_bucket($this->bucket, AmazonS3::REGION_US_E1, AmazonS3::ACL_PUBLIC);
      if(!$res->isOK())
      {
        getLogger()->crit('Could not create S3 bucket: ' . var_export($res, 1));
        return false;
      }
    }

    // TODO add versioning?
    // Set a policy for this bucket only
    $policy = new CFPolicy($this->fs, array(
        'Version' => '2008-10-17',
        'Statement' => array(
            array(
                'Sid' => 'AddPerm',
                'Effect' => 'Allow',
                'Principal' => array(
                    'AWS' => '*'
                ),
                'Action' => array('s3:*'),
                'Resource' => array("arn:aws:s3:::{$this->bucket}/*")
            )
        )
    ));
    $res = $this->fs->set_bucket_policy($this->bucket, $policy);
    if(!$res->isOK())
      getLogger()->crit('Failed to set bucket policy');
    return $res->isOK();
  }

  /**
    * Removes leading slashes since it's not needed when putting new files on S3.
    *
    * @param string $path Path of the photo to have leading slashes removed.
    * @return string
   */
  private function normalizePath($path)
  {
    return preg_replace('/^\/+/', '', $path);
  }
}
