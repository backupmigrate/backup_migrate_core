<?php
/**
 * @file
 * Contains \BackupMigrate\Core\Services\ApplicationInterface.
 */

namespace BackupMigrate\Core\Environment;

use BackupMigrate\Core\Environment\StateInterface;
use BackupMigrate\Core\Environment\CacheInterface;
use BackupMigrate\Core\File\TempFileManagerInterface;
use \Psr\Log\LoggerInterface;

/**
 * Interface ApplicationInterface
 *
 * An interface to describe a service that acts as a gateway to the underlying
 * application.
 *
 * @package BackupMigrate\Core\Services
 */
interface EnvironmentInterface {

  /**
   * @return \BackupMigrate\Core\Environment\CacheInterface;
   */
  public function getCacheManager();

  /**
   * @return \BackupMigrate\Core\Environment\StateInterface;
   */
  public function getStateManager();

  /**
   * @return \BackupMigrate\Core\File\TempFileManagerInterface;
   */
  public function getTempFileManager();

  /**
   * @return \Psr\Log\LoggerInterface;
   */
  public function getLogger();

  /**
   * Get the full ID string for the application.
   *
   * @return string
   */
  public function getIDString();

  /**
   * Get the name of the application
   *
   * @return string
   */
  public function getName();

  /**
   * Get the version number of the application.
   *
   * @return string
   */
  public function getVersion();

  /**
   * Get the URL for the application.
   *
   * @return string
   */
  public function getProjectURL();

}