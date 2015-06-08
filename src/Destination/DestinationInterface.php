<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Destination\DestinationInterface.
 */

namespace BackupMigrate\Core\Destination;

use BackupMigrate\Core\Util\BackupFileReadableInterface;

/**
 * Provides an interface defining a backup source.
 */
interface DestinationInterface
{
  /**
   * Save a file to the destination.
   * 
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $file
   *        The file to save.
   */
  function saveFile(BackupFileReadableInterface $file);

  /**
   * Load the file with the given ID from the destination.
   * 
   * @param string $id The unique identifier for the file. Usually the filename.
   *
   * @return \BackupMigrate\Core\Util\BackupFileReadableInterface The file if it exists or NULL if it doesn't
   */
  public function loadFile($id);

  /**
   * Return a list of files from the destination. This list should be
   * date ordered from newest to oldest.
   * 
   * @param integer $count The number of files to return.
   * @param integer $start The number to start at for pagination.
   * 
   * @return BackupFileInterface[]
   *         An array of BackupFileInterface objects representing the files with
   *         the file ids as keys. The file ids are usually file names but that
   *         is up to the implementing destination to decide. The returned files
   *         may not be readable load file may need to be used to do so.
   */
  public function listFiles($count = 100, $start = 0);

  /**
   * @return int The number of files in the destination.
   */
  public function countFiles();

  /**
   * Does the file with the given id (filename) exist in this destination.
   * 
   * @param string $id The id (usually the filename) of the file.
   * 
   * @return bool True if the file exists, false if it does not.
   */
  public function fileExists($id);

  /**
   * Delete the file with the given id.
   *
   * @param string $id The id of the file to delete.
   */
  public function deleteFile($id);
}
