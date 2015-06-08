<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Util\BackupFileReadableInterface.
 */

namespace BackupMigrate\Core\Util;

/**
 * Provides a service to provision temp files in the correct place for the environment.
 */
interface BackupFileReadableInterface extends BackupFileInterface {

  /**
   * A path or stream that can be used in php file functions.
   * @return string
   */
  public function realpath();
 /**
   * Read a line from the file.
   * 
   * @param int $size The number of bites to read or 0 to read the whole file
   * @return string The data read from the file or NULL if the file can't be read or is at the end of the file.
   */
  public function read($size = 0);

  /**
   * Open a file for reading or writing.
   * 
   * @param bool $binary If true open as a binary file
   */
  public function openForRead($binary = FALSE);

  /**
   * Close a file when we're done reading/writing.
   */
  public function close();

  /**
   * Rewind the file handle to the start of the file.
   */
  public function rewind();
}
