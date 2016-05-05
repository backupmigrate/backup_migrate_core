<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Services\BackupMigrateInterface.
 */

namespace BackupMigrate\Core\Main;

use BackupMigrate\Core\Service\ServiceManager;
use BackupMigrate\Core\Plugin\PluginManagerInterface;
use BackupMigrate\Core\Plugin\PluginCallerInterface;


/**
 * The core Backup and Migrate service.
 */
interface BackupMigrateInterface extends PluginCallerInterface
{

  /**
   * Backup and Migrate constructor.
   */
  public function __construct();

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

  /**
   * Get the list of available destinations.
   *
   * @return PluginManagerInterface
   */
  public function destinations();

  /**
   * Set the destinations plugin manager.
   *
   * @param PluginManagerInterface $destinations
   */
  public function setDestinationManager(PluginManagerInterface $destinations);

  /**
   * Get the list of sources.
   *
   * @return PluginManagerInterface
   */
  public function sources();

  /**
   * Set the sources plugin manager.
   *
   * @param PluginManagerInterface $sources
   */
  public function setSourceManager(PluginManagerInterface $sources);

  /**
   * Get the service locator.
   *
   * @return ServiceManager
   */
  public function services();

  /**
   * Set the service locator.
   *
   * @param ServiceManager $services
   */
  public function setServiceManager($services);
}
