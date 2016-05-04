<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Destination;


use BackupMigrate\Core\File\BackupFileInterface;

/**
 * Interface ListableDestinationInterface
 * @package BackupMigrate\Core\Destination
 */
interface ListableDestinationInterface extends DestinationInterface {
  /**
   * Return a list of files from the destination. This list should be
   * date ordered from newest to oldest.
   *
   * @TODO: Decide if extended metadata should ALWAYS be loaded here. Is there a use case for getting a list of files WITHOUT metadata?
   *
   * @param integer $count The number of files to return.
   * @param integer $start The number to start at for pagination.
   *
   * @return BackupFileInterface[]
   *         An array of BackupFileInterface objects representing the files with
   *         the file ids as keys. The file ids are usually file names but that
   *         is up to the implementing destination to decide. The returned files
   *         may not be readable. Use loadFileForReading to get a readable file.
   */
  public function listFiles($count = 100, $start = 0);

  /**
   * @return int The number of files in the destination.
   */
  public function countFiles();

  /**
   * @return boolean Whether the file exists in this destination
   */
  public function fileExists($id);

  /**
   * Delete the specified file
   */
  public function deleteFile($id);
}
