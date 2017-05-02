<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Plugin\PluginInterface.
 */

namespace BackupMigrate\Core\Plugin;


//use \BackupMigrate\Core\Services\ApplicationInterface;
/**
 * All of the work is done in plugins. Therefore they may need injected:
 *
 * Sources
 * Destinations
 * Other Plugins?
 * Config
 * Application
 *  Cache
 *  State
 * TempFileManager
 *  TempFileAdapter
 *
 *
 */

/**
 * An interface to describe a Backup and Migrate plugin. Plugins take care
 * of all elements of the backup process and can be configured externally.
 */
interface PluginInterface
{
  /**
   * Get a list of supported operations and their weight.
   *
   * An array of operations should take the form:
   *
   * array(
   *  'backup' => array('weight' => 100),
   *  'restore' => array('weight' => -100),
   * );
   *
   * @return array
   */
  public function supportedOps();

  /**
   * Does this plugin implement the given operation.
   *
   * @param $op string The name of the operation
   * @return bool
   */
  public function supportsOp($op);

  /**
   * What is the weight of the given operation for this plugin.

   * @param $op string The name of the operation
   * @return int
   */
  public function opWeight($op);

}
