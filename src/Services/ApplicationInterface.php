<?php
/**
 * @file
 * Contains \BackupMigrate\Core\Services\ApplicationInterface.
 */

namespace BackupMigrate\Core\Services;

use \BackupMigrate\Core\Util\StateInterface;
use \BackupMigrate\Core\Util\CacheInterface;
use \BackupMigrate\Core\Services\TempFileManagerInterface;
use \Psr\Log\LoggerInterface;

/**
 * Interface ApplicationInterface
 *
 * An interface to describe a service that acts as a gateway to the underlying
 * application.
 *
 * @package BackupMigrate\Core\Services
 */
interface ApplicationInterface {

  /**
   * @return \BackupMigrate\Core\Util\CacheInterface;
   */
  public function getCacheManager();

  /**
   * @return \BackupMigrate\Core\Util\StateInterface;
   */
  public function getStateManager();

  /**
   * @return \BackupMigrate\Core\Services\TempFileManagerInterface;
   */
  public function getTempFileManager();

  /**
   * @return \Psr\Log\LoggerInterface;
   */
  public function getLogger();
}