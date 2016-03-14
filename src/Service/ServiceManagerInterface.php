<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Service;

use BackupMigrate\Core\Plugin\PluginInterface;


/**
 * Interface ServiceManagerInterface
 * @package BackupMigrate\Core\Environment
 */
interface ServiceManagerInterface {

  /**
   * Retrieve a service from the locator
   *
   * @param string $type
   *  The service type identifier
   * @return mixed
   */
  public function get($type);

  /**
   * Get an array of keys for all available services.
   *
   * @return array
   */
  public function keys();

  /**
   * Inject the services in this locator into the given plugin.
   *
   * @param object $plugin
   * @return mixed
   */
  public function addClient($plugin);
}