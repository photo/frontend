<?php
interface FileSystemInterface
{
  //public function putDirectory($directoryName);
  public function putFile($localFile, $remoteFile);
  //private function normalizePhoto($raw);
}

