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
trait ConfigurableTrait {
  /**
   * @var \BackupMigrate\Core\Config\ConfigInterface
   */
  protected $config;

  protected $defaults;

  /**
   * @param ConfigInterface|array $init
   *  The initial values for the configurable item
   */
  public function __construct($init = array()) {
    if (is_array($init)) {
      $init = new Config($init);
    }
    if ($init instanceof ConfigInterface) {
      $this->setConfig($init);
    }
  }

  /**
   * Set the configuration for all plugins.
   *
   * @param ConfigInterface $config
   *    A configuration object containing only configuration for all plugins
   */
  public function setConfig(ConfigInterface $config) {
    // Set the configuration object to the one passed in.
    $this->config = $config;

    // Add the default values to the config object so they can be relied on to be always present.
    $this->config()->setDefaults($this->configDefaults());
  }

  /**
   * Get the configuration object for this item.
   * @return \BackupMigrate\Core\Config\ConfigInterface
   */
  public function config() {
    return $this->config ? $this->config : new Config();
  }

  /**
   * Get the default values for the plugin.
   *
   * @return \BackupMigrate\Core\Config\Config
   */
  public function configDefaults() {
    return new Config();
  }

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
  public function configSchema($params = array()) {
    return array();
  }


  /**
   * Get a specific value from the configuration.
   *
   * @param string $key The configuration object key to retrieve.
   * @return mixed The configuration value.
   */
  public function confGet($key) {
    return $this->config()->get($key);
  }

}