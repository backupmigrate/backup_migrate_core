<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Config;

use BackupMigrate\Core\Config\ConfigInterface;


/**
 * Class ConfigurableTrait
 * @package BackupMigrate\Core\Config
 *
 * A configurable object. Manages injection and access to a config object.
 */
interface ConfigurableInterface {
  /**
   * Set the configuration for all plugins.
   *
   * @param ConfigInterface $config
   *    A configuration object containing only configuration for all plugins
   */
  public function setConfig(ConfigInterface $config);

  /**
   * Get the configuration object for this item.
   * @return ConfigInterface
   */
  public function config();

  /**
   * Get a specific value from the configuration.
   *
   * @param string $key The configuration object key to retrieve.
   * @return mixed The configuration value.
   */
  public function confGet($key);

  /**
   * Get the configuration defaults for this item.
   *
   * @return mixed
   * @internal param $key
   */
  public function configDefaults();

  /**
   * Get a default (blank) schema.
   *
   * @param array $params
   *  The parameters including:
   *    - operation - The operation being performed, will be one of:
   *      - 'backup': Configuration needed during a backup operation
   *      - 'restore': Configuration needed during a restore
   *      - 'initialize': Core configuration always needed by this item
   * @return array
   */
  public function configSchema($params = array());

  /**
   * Get any validation errors in the config.
   *
   * @param array $params
   * @return array
   */
  public function configErrors($params = array());

}