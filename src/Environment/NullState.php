<?php
/**
 * @file
 * Contains BackupMigrate\Core\Util\NullState
 */


namespace BackupMigrate\Core\Environment;
use BackupMigrate\Core\Environment\StateInterface;


/**
 * Class NullState
 * @package BackupMigrate\Core\Util
 *
 * A fake state manager. Always returns the default.
 */
class NullState implements StateInterface {

  /**
   * {@inheritdoc}
   */
  public function get($key, $default = NULL) {
    return $default;
  }

  /**
   * {@inheritdoc}
   */
  public function set($key, $value) {
  }

  /**
   * {@inheritdoc}
   */
  public function delete($key) {
  }
}