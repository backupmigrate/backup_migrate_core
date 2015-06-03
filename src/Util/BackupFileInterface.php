<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Util\BackupFileInterface.
 */

namespace BackupMigrate\Core\Util;

/**
 * Provides a metadat-only file object. If the file needs to be readable or
 * writable use \BackupMigrate\Core\Util\BackupFileReadableInterface or
 * \BackupMigrate\Core\Util\BackupFileWritableInterface
 */
interface BackupFileInterface {

  /**
   * Get a metadata value
   *
   * @param string $key The key for the metadata item.
   * @return mixed The value of the metadata for this file.
   */
  public function getMeta($key);

  /**
   * Set a metadata value
   *
   * @param string $key The key for the metadata item.
   * @param mixed $value The value for the metadata item.
   */
  public function setMeta($key, $value);

  /**
   * Set a metadata value
   *
   * @param array $values An array of key-value pairs for the file metadata.
   */
  public function setMetaMultiple($values);

  /**
   * Read a line from the file.
   * 
   * @param int $size The number of bites to read or 0 to read the whole file
   * @return string The data read from the file or NULL if the file can't be read or is at the end of the file.
   */
  public function read($size = 0);

  /**
   * Close a file when we're done reading/writing.
   */
  public function close();

  /**
   * Rewind the file handle to the start of the file.
   */
  public function rewind();

}
