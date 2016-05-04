<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Destination;
use BackupMigrate\Core\File\BackupFileReadableInterface;


/**
 * Interface WritableDestinationInterface
 * @package BackupMigrate\Core\Destination
 */
interface WritableDestinationInterface extends DestinationInterface {
  /**
   * Save a file to the destination.
   *
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $file
   *        The file to save.
   */
  function saveFile(BackupFileReadableInterface $file);
}
