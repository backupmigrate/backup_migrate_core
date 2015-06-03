<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Util\BackupFileInterface.
 */

namespace BackupMigrate\Core\Util;

/**
 * Provides a service to provision temp files in the correct place for the environment.
 */
interface BackupFileWritableInterface extends BackupFileInterface {

  /**
   * Write a line to the file.
   * 
   * @param string $data A string to write to the file.
   */
   public function write($data);

  /**
   * Get a metadata value
   *
   * @param string $key The key for the metadata item.
   * @return mixed The value of the metadata for this file.
   */
  // public function getMeta($key);

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
   * Open a file for reading or writing.
   * 
   * @param bool $binary If true open as a binary file
   */
  public function openForWrite($binary = FALSE);

}
