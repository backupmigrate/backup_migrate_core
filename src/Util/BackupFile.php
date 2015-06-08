<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Util\BackupFile.
 */

namespace BackupMigrate\Core\Util;

use \BackupMigrate\Core\Util\BackupFileInterface;

/**
 * Class BackupFile
 * @package BackupMigrate\Core\Util
 */
class BackupFile implements BackupFileInterface {
  /**
   * The file info (size, timestamp, etc.).
   *
   * @var array
   */
  protected $file_info;

  /**
   * The file path.
   *
   * @var string
   */
  protected $path;

  /**
   * The file name.
   *
   * @var string
   */
  protected $name;

  /**
   * A file handle if it is open.
   *
   * @var resource
   */
  protected $handle;
  
  /**
   * The file's metadata
   * 
   * @var array A key/value associative array of metadata.
   */
  protected $metadata;


  /**
   * Get a metadata value
   *
   * @param string $key The key for the metadata item.
   * @return mixed The value of the metadata for this file.
   */
  public function getMeta($key) {
    return isset($this->metadata[$key]) ? $this->metadata[$key] : NULL;
  }

  /**
   * Set a metadata value
   *
   * @param string $key The key for the metadata item.
   * @param mixed $value The value for the metadata item.
   */
  public function setMeta($key, $value) {
    $this->metadata[$key] = $value;
  }

  /**
   * Set a metadata value
   *
   * @param array $values An array of key-value pairs for the file metadata.
   */
  public function setMetaMultiple($values) {
    foreach ((array)$values as $key => $value) {
      $this->setMeta($key, $value);
    }
  }

  /**
   * Get all metadata
   *
   * @param array $values An array of key-value pairs for the file metadata.
   */
  public function getMetaAll() {
    return $this->metadata;
  }
}
