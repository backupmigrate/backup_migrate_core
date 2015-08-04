<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Service;

use BackupMigrate\Core\Service\ServiceInterface;


/**
 * Interface ServiceLocatorInterface
 * @package BackupMigrate\Core\Environment
 */
interface ServiceLocatorInterface {

  /**
   * Retrieve a service from the locator
   *
   * @param string $type
   *  The service type identifier
   * @return ServiceInterface
   */
  public function get($type);

  /**
   * Get an array of keys for all available services.
   *
   * @return array
   */
  public function keys();
}