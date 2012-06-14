<?php
class Upgrade extends BaseModel
{
  private $scriptsDir,
    $systems,
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

  public function __construct($params = null)
  {
    parent::__construct();
    if(isset($params['config']))
      $this->config = $params['config'];

    $this->scriptsDir = sprintf('%s/upgrade', $this->config->paths->configs);
    $this->systems = array('readme','base','db','fs');
    $defaults = $this->config->defaults;
    $this->currentCodeVersion = $defaults->currentCodeVersion;
    $currentParts = explode('.', $this->currentCodeVersion);
    $this->currentCodeMajorVersion = $currentParts[0];
    $this->currentCodeMinorVersion = $currentParts[1];
    $this->currentCodeTrivialVersion = $currentParts[2];

    $siteConfig = $this->config->site;
    if(isset($siteConfig->lastCodeVersion) && !empty($siteConfig->lastCodeVersion))
      $this->lastCodeVersion = $siteConfig->lastCodeVersion;
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

  public function getUpgradeVersions($systems = null)
  {
    if($systems === null)
      $systems = $this->systems;

    $scriptsExist = false;
    $scripts = array();
    $databases = $this->db->identity();
    $databaseVersion = $this->db->version();
    // Backwards compatibility
    // Before the upgrade code the database was versioned as an int
    if(!preg_match('/\d\.\d\.\d/', $databaseVersion))
      $databaseVersion = '1.2.0';
    $databaseVersionParts = explode('.', $databaseVersion);
    $databaseMajorVersion = $databaseVersionParts[0];
    $databaseMinorVersion = $databaseVersionParts[1];
    $databaseTrivialVersion = $databaseVersionParts[2];

    // TODO: Move the logic to search directories into a function
     
    // readme
    if(in_array('readme', $systems))
    {
      $scripts['readme'] = array();
      $dir = dir($dirname = sprintf('%s/readme', $this->scriptsDir));
      while (false !== ($entry = $dir->read()))
      {
        if($entry == '.' || $entry == '..')
          continue;

        if(preg_match('/\d\.\d\.\d/', $entry, $matches))
        {
          $version = $matches[0];
          $versionParts = explode('.', $version);
          if($versionParts[0] > $this->lastCodeMajorVersion)
          {
            $scripts['readme'][$version] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
          elseif($versionParts[0] == $this->lastCodeMajorVersion && $versionParts[1] > $this->lastCodeMinorVersion)
          {
            $scripts['readme'][$version] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
          elseif($versionParts[0] == $this->lastCodeMajorVersion && $versionParts[1] == $this->lastCodeMinorVersion && $versionParts[2] > $this->lastCodeTrivialVersion)
          {
            $scripts['readme'][$version] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
        }
      }
    }

    // base
    if(in_array('base', $systems))
    {
      $scripts['base'] = array();
      $dir = dir($dirname = sprintf('%s/base', $this->scriptsDir));
      while (false !== ($entry = $dir->read()))
      {
        if($entry == '.' || $entry == '..')
          continue;

        if(preg_match('/\d\.\d\.\d/', $entry, $matches))
        {
          $version = $matches[0];
          $versionParts = explode('.', $version);
          if($versionParts[0] > $this->lastCodeMajorVersion)
          {
            $scripts['base'][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
          elseif($versionParts[0] == $this->lastCodeMajorVersion && $versionParts[1] > $this->lastCodeMinorVersion)
          {
            $scripts['base'][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
          elseif($versionParts[0] == $this->lastCodeMajorVersion && $versionParts[1] == $this->lastCodeMinorVersion && $versionParts[2] > $this->lastCodeTrivialVersion)
          {
            $scripts['base'][$version][] = sprintf('%s/%s', $dirname, $entry);
            $scriptsExist = true;
          }
        }
        ksort($scripts['base']);
      }
    }

    // database
    if(in_array('db', $systems))
    {
      $scripts['db'] = array();
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
            if($versionParts[0] > $databaseMajorVersion)
            {
              $scripts['db'][$database][$version][] = sprintf('%s/%s', $dirname, $entry);
              $scriptsExist = true;
            }
            elseif($versionParts[0] == $databaseMajorVersion && $versionParts[1] > $databaseMinorVersion)
            {
              $scripts['db'][$database][$version][] = sprintf('%s/%s', $dirname, $entry);
              $scriptsExist = true;
            }
            elseif($versionParts[0] == $databaseMajorVersion && $versionParts[1] == $databaseMinorVersion && $versionParts[2] > $databaseTrivialVersion)
            {
              $scripts['db'][$database][$version][] = sprintf('%s/%s', $dirname, $entry);
              $scriptsExist = true;
            }
          }
        }
        ksort($scripts['db'][$database]);
      }
    }

    /*
    if(in_array('fs', $systems))
    {
      $scripts['fs'] = array();
      $filesystems = getFs()->identity();
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
      }
    }
    */

    if($scriptsExist)
      return $scripts;
    return false;
  }

  public function isCurrent()
  {
    return $this->currentCodeVersion == $this->lastCodeVersion;
  }

  public function performUpgrade($systems = null)
  {
    if($systems === null)
      $systems = $this->systems;
    if(isset($systems['readme']))
      unset($systems['readme']);

    $scripts = $this->getUpgradeVersions();
    if($scripts === false)
      return true;

    if(isset($scripts['base']) && in_array('base', $systems))
    {
      foreach($scripts['base'] as $version => $versions)
      {
        foreach($versions as $file)
        {
          $this->logger->info(sprintf('Calling executeScript on base file %s', $file));
          $this->executeScript($file);
        }
      }
    }

    if(isset($scripts['db']) && in_array('db', $systems))
    {
      foreach($scripts['db'] as $database => $versions)
      {
        foreach($versions as $version)
        {
          foreach($version as $file)
          {
            $this->logger->info(sprintf('Calling executeScript on %s file %s', $database, $file));
            $this->db->executeScript($file, $database);
          }
        }
      }
    }

    if(isset($scripts['fs']) && in_array('fs', $systems))
    {
      foreach($scripts['fs'] as $filesystem => $versions)
      {
        foreach($versions as $version)
        {
          foreach($version as $file)
          {
            $this->logger->info(sprintf('Calling executeScript on %s file %s', $filesystem, $file));
            $this->fs->executeScript($file, $filesystem);
          }
        }
      }
    }
  }

  private function executeScript($file)
  {
    include $file;
  }
}
