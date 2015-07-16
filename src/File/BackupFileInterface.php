<?php

/**
 * @file
 * Contains \BackupMigrate\Core\File\BackupFileInterface.
 */

namespace BackupMigrate\Core\File;

/**
 * Provides a metadata-only file object. If the file needs to be readable or
 * writable use \BackupMigrate\Core\File\BackupFileReadableInterface or
 * \BackupMigrate\Core\File\BackupFileWritableInterface
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
   * Set the file name without extension.
   *
   * @param string $name
   */
  public function setName($name);

  /**
   * Get the file name without extension.
   *
   * @return string
   */
  public function getName();

  /**
   * Get the full filename with extensions.
   *
   * @return string
   *  The full filename (with extension, without filepath)
   */
  public function getFullName();


  /**
   * Set the full filename with extensions.
   *
   * @param string $fullname
   *  The full filename (with extension, without filepath)
   */
  public function setFullName($fullname);

  /**
   * Get an array of file extensions.
   *
   * For example: testfile.txt.gz would return: ['txt', 'gz']
   *
   * @return array
   */
  public function getExtList();

  /**
   * Get the last file extension
   *
   * For example: testfile.txt.gz would return: 'gz'
   *
   * @return mixed
   */
  public function getExtLast();

  /**
   * Get the full file extension
   *
   * For example: testfile.txt.gz would return: 'txt.gz'
   * @return mixed
   */
  public function getExt();

  /**
   * Set the extension array for the file to the given array.
   *
   * @param array $ext
   *  The list of file extensions for the file
   */
  public function setExtList($ext);

}
