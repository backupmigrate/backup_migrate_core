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
   * Get the configuration object for this item.
   * @return \BackupMigrate\Core\Config\ConfigInterface
   */
  public function config() {
    return $this->config ? $this->config : new Config();
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

  /**
   * Get the default value for a specific key
   *
   * @param string $key The configuration object key to retrieve
   * @return mixed The default configuration value.
   */
  public function confDefault($key) {
    $function = '_confDefault_'. $key;
    if (function_exists($function)) {
      return $function();
    }
    if (!empty($this->defaults[$key])) {
      return $this->defaults[$key];
    }
    return NULL;
  }
}