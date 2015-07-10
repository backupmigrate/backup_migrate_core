<?php
/**
 * @file
 * Contains BackupMigrate\Core\Util\NullCache
 */

namespace BackupMigrate\Core\Environment;

/**
 * Class NullCache
 * @package BackupMigrate\Core\Util
 *
 * Does nothing. Can be used when there is no cache system in place.
 */
class NullCache implements CacheInterface {

  /**
   * {@inheritdoc}
   */
  public function get($cid, $allow_invalid = FALSE) {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function set($cid, $data, $expire = CacheInterface::CACHE_PERMANENT) {
  }

  /**
   * {@inheritdoc}
   */
  public function delete($cid) {
  }
}