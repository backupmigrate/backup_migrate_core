<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Services\BackupMigrateInterface.
 */

namespace BackupMigrate\Core\Services;

use \BackupMigrate\Core\Source;
use \BackupMigrate\Core\Destination;
use \BackupMigrate\Core\Config;


/**
 * The core Backup and Migrate service.
 */
class BackupMigrateInterface
{

  /**
   * Peform the backup from a given source and save it to the given destination.
   * 
   * @param SourceInterface $source The source (eg: database, file directory) to be backed up
   * @param ConfigInterface $settings The settings to be used during the operation
   * @param DestinationInterface $destination The place to save the backup file to.
   */
  public function backup(SourceInterface $source, DestinationInterface $destination, ConfigInterface $settings);

  /**
   * Peform the backup from a given source and save it to the given destination.
   * 
   * @param SourceInterface $source The source (eg: database, file directory) to be restored
   * @param DestinationInterface $destination The destination where the backup file is stored
   * @param ConfigInterface $settings The settings to be used during the operation
   * @param string $file The ID of the file to be restored. Only optional when the destination
   *                     does not store multiple files (like browser upload)
   */
  public function restore(SourceInterface $source, DestinationInterface $destination, ConfigInterface $settings, $file = NULL);


}
