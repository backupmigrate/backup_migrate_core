<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Plugin\PluginManagerInterface.
 */

namespace BackupMigrate\Core\Plugin;

use \BackupMigrate\Core\Config\ConfigInterface;

/**
 * Manage all of the available Plugins.
 */
interface PluginManagerInterface
{
  /**
   * Add an item to the manager
   *
   * @param \BackupMigrate\Core\Plugin\PluginInterface|object $item
   *    The source to add.
   * @param $id
   * @return
   */
  public function add(PluginInterface $item, $id);

  /**
   * Get the item with the given id.
   *
   * @param $id
   * @return \BackupMigrate\Core\Base\ModelBaseInterface The item specified by the id or NULL if it doesn't exist.
   **/
  public function get($id);

  /**
   * Get a list of all of the items.
   *
   * @return \BackupMigrate\Core\Plugin\PluginInterface[] An ordered list of the sources, keyed by their id.
   */
  public function getAll();

  /**
   * Set the configuration for all plugins.
   * 
   * @param ConfigInterface $config A configuration object containing only configuration for all plugins
   */
  public function setConfig(ConfigInterface $config);

  /**
   * Get all plugins that implement the given operation.
   *
   * @param string $op The name of the operation.
   * @return \BackupMigrate\Core\Plugin\PluginInterface[]
   */
  public function getAllByOp($op);

  /**
   * Get the app (essentially a dependency injection container for interfacing
   * with the broader app and environment)
   *
   * @return \BackupMigrate\Core\Services\ApplicationInterface
   */
  public function getApp();

  /**
   * Get the list of supported file types, optionally for the specified op.
   *
   * @param string|null $op
   *    The operation for which the file types are supported.
   * @return array
   */
  public function supportedFileTypes($op = NULL);

}
