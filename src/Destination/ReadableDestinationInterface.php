<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Destination;

use BackupMigrate\Core\File\BackupFileInterface;

/**
 * Interface ReadableDestinationInterface
 * @package BackupMigrate\Core\Destination
 */
interface ReadableDestinationInterface extends DestinationInterface {

  /**
   * Get a file object representing the file with the given ID from the destination.
   * This file item will not necessarily be readable nor will it have extended
   * metadata loaded. Use loadForReading and loadFileMetadata to get those.
   *
   * @TODO: Decide if extended metadata should ALWAYS be loaded here.
   *
   * @param string $id The unique identifier for the file. Usually the filename.
   *
   * @return \BackupMigrate\Core\File\BackupFileInterface
   *    The file if it exists or NULL if it doesn't
   */
  public function getFile($id);

  /**
   * Load the metadata for the given file however it may be stored.
   *
   * @param \BackupMigrate\Core\File\BackupFileInterface $file
   * @return \BackupMigrate\Core\File\BackupFileInterface
   */
  public function loadFileMetadata(BackupFileInterface $file);

  /**
   * Load the file with the given ID from the destination.
   *
   * @param \BackupMigrate\Core\File\BackupFileInterface $file
   * @return \BackupMigrate\Core\File\BackupFileReadableInterface The file if it exists or NULL if it doesn't
   */
  public function loadFileForReading(BackupFileInterface $file);


  /**
   * Does the file with the given id (filename) exist in this destination.
   *
   * @param string $id The id (usually the filename) of the file.
   *
   * @return bool True if the file exists, false if it does not.
   */
  public function fileExists($id);
}