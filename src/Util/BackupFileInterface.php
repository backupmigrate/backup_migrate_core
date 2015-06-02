<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Services\BackupFileInterface.
 */

namespace BackupMigrate\Core\Services;

/**
 * Provides a service to provision temp files in the correct place for the environment.
 */
interface BackupFileInterface {

  /**
   * Read a line from the file.
   * 
   * @param int $size The number of bites to read or 0 to read the whole file
   * @return string The data read from the file or NULL if the file can't be read or is at the end of the file.
   */
  public function read($size = 0);

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
   * Open a file for reading or writing.
   * 
   * @param bool $write If tre open for writing, otherwise open for reading only
   * @param bool $binary If true open as a binary file
   * @return resource A file handle that can be used for fread or fwrite.
   */
  public function open($write = FALSE, $binary = FALSE);

  /**
   * Close a file when we're done reading/writing.
   */
  public function close();

  /**
   * Rewind the file handle to the start of the file.
   */
  public function rewind();

}
