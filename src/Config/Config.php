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
class Config implements ConfigInterface {

  /**
   * @var array
   */
  protected $config;


  /**
   * @param array $init
   */
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
   * @param mixed $default
   *  The default to return if the value does not exist.
   * @return mixed The value of the setting.
   */
  public function get($key, $default = NULL) {
    return $this->keyIsSet($key) ? $this->config[$key] : $default;
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
   * Determine if the given key has had a value set for it.
   *
   * @param $key
   * @return bool
   */
  public function keyIsSet($key) {
    return isset($this->config[$key]);
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