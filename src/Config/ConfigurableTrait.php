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
   * @param ConfigInterface $config A configuration object containing only configuration for all plugins
   */
  public function setConfig(ConfigInterface $config) {
    $this->config = $config;
  }

  /**
   * Set the configuration for all plugins.
   *
   * @param ConfigInterface $config A configuration object containing only configuration for all plugins
   */
  public function setDefaults(ConfigInterface $defaults) {
    $this->defaults = $defaults;
  }

  /**
   * Get the configuration object for this item.
   * @return \BackupMigrate\Core\Config\ConfigInterface
   */
  public function config() {
    return $this->config ? $this->config : new Config();
  }

  /**
   * Get the configuration object for this item.
   * @return \BackupMigrate\Core\Config\ConfigInterface
   */
  public function defaults() {
    if (!$this->defaults) {
      $this->setDefaults($this->confDefaults());
    }
    return $this->defaults;
  }

  /**
   * Get the default values for the plugin.
   *
   * @return \BackupMigrate\Core\Config\Config
   */
  public function confDefaults() {
    return new Config();
  }

  /**
   * Get a default (blank) schema.
   *
   * @return array
   */
  public function configSchema() {
    return [];
  }

  /**
   * Get a specific value from the configuration.
   *
   * @param string $key The configuration object key to retrieve.
   * @return mixed The configuration value.
   */
  public function confGet($key) {
    if ($this->config()->keyIsSet($key)) {
      return $this->config()->get($key);
    }
    else {
      return $this->defaults()->get($key);
    }
  }

}