<?php
/**
 * @file
 * Contains \BackupMigrate\Core\Profile\ConfigInterface.
 */

namespace BackupMigrate\Core\Config;

use \BackupMigrate\Core\Config\ConfigInterface;

/**
 * Class ConfigBase
 *
 * A basic configuration manager with very little logic in it.
 *
 * @package BackupMigrate\Core\Config
 */
class ConfigBase implements ConfigInterface {

  /**
   * @var array
   */
  protected $config;


  public function __construct($init = array()) {
    if ($init instanceof ConfigInterface) {
      $this->fromArray($init->toArray());
    }
    else if (is_array($init)) {
      $this->fromArray($init);
    }
  }

  /**
   * Get a setting value
   *
   * @param string $key The key for the setting.
   * @return mixed The value of the setting.
   */
  public function get($key) {
    return isset($this->config[$key]) ? $this->config[$key] : NULL;
  }

  /**
   * Set a setting value
   *
   * @param string $key The key for the setting.
   * @param mixed $value The value for the setting.
   */
  public function set($key, $value) {
    $this->config[$key] = $value;
  }

  /**
   * Get all settings as an associative array
   *
   * @return array All of the settings in this profile
   */
  public function toArray() {
    return $this->config;
  }

  /**
   * Set all from an array
   *
   * @param array $values An associative array of settings.
   */
  public function fromArray($values) {
    $this->config = $values;
  }
}