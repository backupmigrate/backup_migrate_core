<?php
/**
 * @file
 * Contains \BackupMigrate\Core\Util\StateInterface.
 */

namespace BackupMigrate\Core\Environment;

/**
 * Defines the interface for the state system. If a permanent state storage
 * system is provided then Backup and Migrate will store some state variables
 * to help improve functionality.
 *
 * @ingroup state_api
 */
interface StateInterface {

  /**
   * Returns the stored value for a given key.
   *
   * @param string $key
   *   The key of the data to retrieve.
   * @param mixed $default
   *   The default value to use if the key is not found.
   *
   * @return mixed
   *   The stored value, or NULL if no value exists.
   */
  public function get($key, $default = NULL);

  /**
   * Saves a value for a given key.
   *
   * @param string $key
   *   The key of the data to store.
   * @param mixed $value
   *   The data to store.
   */
  public function set($key, $value);

  /**
   * Deletes an item.
   *
   * @param string $key
   *   The item name to delete.
   */
  public function delete($key);


}
