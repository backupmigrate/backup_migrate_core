<?php
/**
 * @file
 */
namespace BackupMigrate\Core\Config;


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
   * @param ConfigInterface $config A configuration object containing only configuration for all plugins
   */
  public function setConfig(ConfigInterface $config);

  /**
   * Get the configuration object for this item.
   * @return \BackupMigrate\Core\Config\ConfigInterface
   */
  public function config();

  /**
   * Get a specific value from the configuration.
   *
   * @param string $key The configuration object key to retrieve.
   * @return mixed The configuration value.
   */
  public function confGet($key);
}