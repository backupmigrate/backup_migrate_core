<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Util\BackupFileInterface.
 */

namespace BackupMigrate\Core\Util;

/**
 * Provides a metadata-only file object. If the file needs to be readable or
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
   * Get all meta data as an array
   *
   * @return array $values An array of key-value pairs for the file metadata.
   */
  public function getMetaAll();

  /**
   * Get an array of file extensions.
   *
   * For example: testfile.txt.gz would return:
   * ['txt', 'gz']
   *
   * @return array
   */
  public function getExtList();

  /**
   * Get the last file extension
   *
   * For example: testfile.txt.gz would return:
   * ['txt', 'gz']
   * @return mixed
   */
  public function getExtLast();
}
