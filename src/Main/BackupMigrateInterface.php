<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Services\BackupMigrateInterface.
 */

namespace BackupMigrate\Core\Main;

use BackupMigrate\Core\Service\EnvironmentInterface;
use BackupMigrate\Core\Plugin\PluginInterface;
use BackupMigrate\Core\Plugin\PluginManager;
use \BackupMigrate\Core\Source\SourceManagerInterface;
use \BackupMigrate\Core\Source\SourceInterface;
use \BackupMigrate\Core\Destination\DestinationManagerInterface;
use \BackupMigrate\Core\Plugin\PluginManagerInterface;
use \BackupMigrate\Core\Config\ConfigInterface;


/**
 * The core Backup and Migrate service.
 */
interface BackupMigrateInterface
{

  /**
   * Backup and Migrate constructor. Takes all of the dependencies for this service.
   *
   * @param \BackupMigrate\Core\Plugin\PluginManager $plugins
   *  A plugin manager containing all of the available plugins for this service.
   */
  public function __construct(PluginManager $plugins = NULL);

  /**
   * Perform the backup from a given source and save it to the given destination.
   *
   * @param string $source_id The id of the source to backup
   * @param string $destination_id The id of the destination to save the backup to.
   * @return
   */
  public function backup($source_id, $destination_id);

  /**
   * Perform the restore to a given source loading it from the given file in the given destination.
   *
   * @param string $source_id The id of the source to restore
   * @param string $destination_id The id of the destination to read the backup from.
   * @param string $file The ID of the file to be restored. Only optional when the destination
   *                     does not store multiple files (like browser upload)
   */
  public function restore($source_id, $destination_id, $file = NULL);


}
