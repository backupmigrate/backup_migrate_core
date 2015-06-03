<?php
/**
 * @file
 * Contains \BackupMigrate\Core\Profile\ConfigInterface.
 */

namespace BackupMigrate\Core\Config;

/**
 * Provides an interface defining a backup source.
 */
interface ConfigInterface
{

  /**
   * Get a setting value
   *
   * @param string $key The key for the setting.
   * @return mixed The value of the setting.
   */
  public function getSetting($key);

  /**
   * Set a setting value
   *
   * @param string $key The key for the setting.
   * @param mixed $value The value for the setting.
   */
  public function setSetting($key, $value);

  /**
   * Get all settings as an associative array
   *
   * @return array All of the settings in this profile
   */
  public function toArray();


  /**
   * Set all from an array
   *
   * @param array $values An associative array of settings.
   */
  public function fromArray($values);


}
