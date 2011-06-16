<?php
interface FileSystemInterface
{
  public function deletePhoto($id);
  public function putPhoto($localFile, $remoteFile);
  //private function normalizePhoto($raw);
}

