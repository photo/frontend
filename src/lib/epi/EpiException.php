<?php
/**
 * @author Jaisen Mathai <jaisen@jmathai.com>
 * @uses Exception
 */
class EpiException extends Exception
{
  public static function raise($exception)
  {
    $useExceptions = Epi::getSetting('exceptions');
    if($useExceptions)
    {
      throw new $exception($exception->getMessage(), $exception->getCode());
    }
    else
    {
      echo sprintf("An error occurred and you have <strong>exceptions</strong> disabled so we're displaying the information.
                    To turn exceptions on you should call: <em>Epi::setSetting('exceptions', true);</em>.
                    <ul><li>File: %s</li><li>Line: %s</li><li>Message: %s</li><li>Stack trace: %s</li></ul>",
                    $exception->getFile(), $exception->getLine(), $exception->getMessage(), nl2br($exception->getTraceAsString()));
    }
  }
}
class EpiCacheException extends EpiException{}
class EpiCacheTypeDoesNotExistException extends EpiCacheException{}
class EpiCacheMemcacheClientDneException extends EpiCacheException{}
class EpiCacheMemcacheConnectException extends EpiCacheException{}
class EpiDatabaseException extends EpiException{}
class EpiDatabaseConnectionException extends EpiDatabaseException{}
class EpiDatabaseQueryException extends EpiDatabaseException{}
class EpiSessionException extends EpiException{}
