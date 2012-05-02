<?php
/**
 * RemoteStorage base class
 *
 * @author Michiel de Jong <michiel@unhosted.org>
 */

class RemoteStorage {
  private $picturesBaseUrl;
  private $api;
  private $token;
  function __construct($setPicturesBaseUrl, $setApi, $setToken) {
    $this->picturesBaseUrl = $setPicturesBaseUrl;
    $this->api = $setApi;
    $this->token = $setToken;
  }
  function doCurl($verb, $remotePath, $dataFile=null) {
    getLogger()->warn("doCurl {$verb} {$this->picturesBaseUrl}{$remotePath} {$dataFile}");
    $ch = curl_init($this->picturesBaseUrl.$remotePath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->token));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if($verb=='PUT') {
      curl_setopt($ch, CURLOPT_PUT, 1);
      $fp = fopen($dataFile, 'r');
      if($fp) {
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($dataFile));
      } else {
        return false;
      }
    } else if($verb=='GET') {
      $fp = fopen($dataFile, 'w');
      if($fp) {
        curl_setopt($ch, CURLOPT_FILE, $fp);
      } else {
        return false;
      }
    } else {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
    }
    $result = curl_exec($ch);
    $resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    var_dump($result);
    var_dump($resultCode);
    return $result;
  }
  function deleteItem($remotePath) {
    getLogger()->warn("deleteItem {$remotePath}");
    $result = $this->doCurl('DELETE', $remotePath);
    //return $result;
    return true; 
  }
  function fetchItem($remotePath, $localPath) {
    getLogger()->warn("fetchItemSync {$remotePath} {$localPath}");
    $result = $this->doCurl('GET', $remotePath, $localPath);
    return $result;
  }
  function pushItem($localPath, $remotePath) {
    getLogger()->warn("pushItemSync {$localPath} {$remotePath}");
    $result = $this->doCurl('PUT', $remotePath, $localPath);
    return $result;
  }
}
