<?php
class Upgrade
{
  private $scriptsDir,
    $currentCodeVersion, // version of the source code
    $currentCodeMajorVersion,
    $currentCodeMinorVersion,
    $currentCodeTrivialVersion,
    $lastCodeVersion, // version of the source code
    $dbVersion, // database version
    $dbMajorVersion,
    $dbMinorVersion,
    $dbTrivialVersion,
    $fsVersion, // filesystem version
    $fsMajorVersion,
    $fsMinorVersion,
    $fsTrivialVersion;

  public function __construct()
  {
    $this->scriptsDir = sprintf('%s/upgrade', getConfig()->get('paths')->configs);
    $defaults = getConfig()->get('defaults');
    $this->currentCodeVersion = $defaults->currentCodeVersion;
    $currentParts = explode('.', $this->currentCodeVersion);
    $this->currentCodeMajorVersion = $currentParts[0];
    $this->currentCodeMinorVersion = $currentParts[1];
    $this->currentCodeTrivialVersion = $currentParts[2];

    $siteConfig = getConfig()->get('site');
    if(isset($siteConfig->lastCodeVersion) && !empty($siteConfig->lastCodeVersion))
      $this->lastCodeVersion = getConfig()->get('site')->lastCodeVersion;
    else
      $this->lastCodeVersion = $defaults->lastCodeVersion;
    $lastParts = explode('.', $this->lastCodeVersion);
    $this->lastCodeMajorVersion = $lastParts[0];
    $this->lastCodeMinorVersion = $lastParts[1];
    $this->lastCodeTrivialVersion = $lastParts[2];
  }

  public function getCurrentVersion()
  {
    return $this->currentCodeVersion;
  }

  public function getLastVersion()
  {
    return $this->lastCodeVersion;
  }

  public function isCurrent()
  {
    return $this->currentCodeVersion == $this->lastCodeVersion;
  }

  public function performUpgrade($systems = array('db','fs'))
  {
    $scripts = $this->getUpgradeVersions();
    if($scripts === false)
      return true;

    if(in_array('db', $systems))
    {
      foreach($scripts['db'] as $database => $versions)
      {
        foreach($versions as $version)
        {
          foreach($version as $file)
          {
            getDb()->executeScript($file, $database);
          }
        }
      }
    }

    if(in_array('fs', $systems))
    {
      foreach($scripts['fs'] as $filesystem => $versions)
      {
        foreach($versions as $version)
        {
          foreach($version as $file)
          {
            getFs()->executeScript($file, $filesystem);
          }
        }
      }
    }
  }

  private function getUpgradeVersions()
  {
    $scriptsExist = false;
    $scripts = array('db' => array(), 'fs' => array());
    $databases = getDb()->identity();
    $databaseVersion = getDb()->version();
    // Backwards compatibility
    // Before the upgrade code the database was versioned as an int
    if(!preg_match('/\d\.\d\.\d\./', $databaseVersion))
      $databaseVersion = '1.2.0';
    $databaseVersionParts = explode('.', $databaseVersion);
    $databaseMajorVersion = $databaseVersionParts[0];
    $databaseMinorVersion = $databaseVersionParts[1];
    $databaseTrivialVersion = $databaseVersionParts[2];

    foreach($databases as $database)
    {
      $scripts['db'][$database] = array();
      $dir = dir($dirname = sprintf('%s/db/%s', $this->scriptsDir, $database));
      while (false !== ($entry = $dir->read()))
      {
        if($entry == '.' || $entry == '..')
          continue;

        if(preg_match('/\d\.\d\.\d/', $entry, $matches))
        {
          $version = $matches[0];
          $versionParts = explode('.', $version);
          if($versionParts[0] > $databaseMajorVersion && $versionParts[0] <= $this->currentCodeMajorVersion)
          {
            $scripts['db'][$database][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
          elseif($versionParts[1] > $databaseMinorVersion && $versionParts[1] <= $this->currentCodeMinorVersion)
          {
            $scripts['db'][$database][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
          elseif($versionParts[2] > $databaseTrivialVersion && $versionParts[2] <= $this->currentCodeTrivialVersion)
          {
            $scripts['db'][$database][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
        }
      }
    }

    /*$filesystems = getFs()->identity();
    $filesysemVersion = getFs()->version();
    foreach($filesystems as $filesystem)
    {
      $scripts['fs'][$filesystem] = array();
      $dir = dir($dirname = sprintf('%s/fs/%s', $this->scriptsDir, $filesystem));
      while (false !== ($entry = $dir->read()))
      {
        if($entry == '.' || $entry == '..')
          continue;

        if(preg_match('/\d\.\d\.\d/', $entry, $matches))
        {
          $version = $matches[0];
          $versionParts = explode('.', $version);
          if($versionParts[0] > $this->lastCodeMajorVersion && $versionParts[0] <= $this->currentCodeMajorVersion)
          {
            $scripts['fs'][$filesystem][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
          elseif($versionParts[1] > $this->lastCodeMinorVersion && $versionParts[1] <= $this->currentCodeMinorVersion)
          {
            $scripts['fs'][$filesystem][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
          elseif($versionParts[2] > $this->lastCodeTrivialVersion && $versionParts[2] <= $this->currentCodeTrivialVersion)
          {
            $scripts['fs'][$filesystem][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
        }
      }
    }*/

    if($scriptsExist)
      return $scripts;
    return false;
  }
}

function getUpgrade()
{
  static $upgrade;
  if(!$upgrade)
    $upgrade = new Upgrade;

  return $upgrade;
}
