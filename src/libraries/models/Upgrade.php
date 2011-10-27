<?php
class Upgrade
{
  private $scriptsDir,
    $currentVersion, // version of the source code
    $currentMajorVersion,
    $currentMinorVersion,
    $currentTrivialVersion,
    $lastVersion, // version since last upgrade
    $lastMajorVersion,
    $lastMinorVersion,
    $lastTrivialVersion;

  public function __construct()
  {
    $this->scriptsDir = sprintf('%s/upgrade', getConfig()->get('paths')->configs);
    $defaults = getConfig()->get('defaults');
    $this->currentVersion = $defaults->currentVersion;
    $siteConfig = getConfig()->get('site');
    if(isset($siteConfig->lastVersion) && !empty($siteConfig->lastVersion))
      $this->lastVersion = getConfig()->get('site')->lastVersion;
    else
      $this->lastVersion = $defaults->lastVersion;

    $currentParts = explode('.', $this->currentVersion);
    $this->currentMajorVersion = $currentParts[0];
    $this->currentMinorVersion = $currentParts[1];
    $this->currentTrivialVersion = $currentParts[2];

    $lastParts = explode('.', $this->lastVersion);
    $this->lastMajorVersion = $lastParts[0];
    $this->lastMinorVersion = $lastParts[1];
    $this->lastTrivialVersion = $lastParts[2];
  }

  public function getCurrentVersion()
  {
    return $this->currentVersion;
  }

  public function getLastVersion()
  {
    return $this->lastVersion;
  }

  public function isCurrent()
  {
    return $this->currentVersion == $this->lastVersion;
  }

  public function performUpgrade()
  {
    $scripts = $this->getUpgradeVersions();
    if($scripts === false)
      return true;

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

  private function getUpgradeVersions()
  {
    $scriptsExist = false;
    $scripts = array('db' => array(), 'fs' => array());
    $databases = getDb()->identity();
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
          if($versionParts[0] > $this->lastMajorVersion && $versionParts[0] <= $this->currentMajorVersion)
          {
            $scripts['db'][$database][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
          elseif($versionParts[1] > $this->lastMinorVersion && $versionParts[1] <= $this->currentMinofVersion)
          {
            $scripts['db'][$database][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
          elseif($versionParts[2] > $this->lastTrivialVersion && $versionParts[2] <= $this->currentTrivialVersion)
          {
            $scripts['db'][$database][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
        }
      }
    }

    $filesystems = getFs()->identity();
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
          if($versionParts[0] > $this->lastMajorVersion && $versionParts[0] <= $this->currentMajorVersion)
          {
            $scripts['fs'][$filesystem][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
          elseif($versionParts[1] > $this->lastMinorVersion && $versionParts[1] <= $this->currentMinofVersion)
          {
            $scripts['fs'][$filesystem][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
          elseif($versionParts[2] > $this->lastTrivialVersion && $versionParts[2] <= $this->currentTrivialVersion)
          {
            $scripts['fs'][$filesystem][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
        }
      }
    }

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
