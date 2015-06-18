<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Services\BackupMigrate.
 */

namespace BackupMigrate\Core\Services;

use \BackupMigrate\Core\Config\ConfigInterface;
use \BackupMigrate\Core\Plugin\PluginCallerInterface;
use \BackupMigrate\Core\Plugin\PluginCallerTrait;
use \BackupMigrate\Core\Plugin\PluginInterface;
use \BackupMigrate\Core\Plugin\PluginManager;

/**
 * The core Backup and Migrate service.
 *
 * Usage:
 *   // Instantiate an application container to provide access to the file system etc.
 *   $app = new ApplicationBase();
 *   // Pass in the configuration
 *   $config = new ConfigBase(...);
 *   $bam = new BackupMigrate($app, $config);
 *   $bam->plugins()->add(new MySQLSource(...), 'db');
 *   $bam->plugins()->add(new MySQLSource(...), 'another');
 *   $bam->plugins()->add(new DirectoryDestination(...), 'manual');
 *   $bam->plugins()->add(new CompressionPlugin(), 'encryption');
 *   $bam->backup($from, to);
 */
class BackupMigrate implements BackupMigrateInterface, PluginCallerInterface
{
  use PluginCallerTrait;

  /**
   * {@inheritdoc}
   */
  function __construct(EnvironmentInterface $app, ConfigInterface $config = NULL) {
    $this->setPluginManager(new PluginManager($app, $config));
  }


  /**
   * {@inheritdoc}
   */
  public function backup($source_id, $destination_id) {

    // Get the source and the destination to use.
    $source = $this->plugins()->get($source_id);
    $destination = $this->plugins()->get($destination_id);

    // @TODO Check the source and destination and throw appropriate exceptions.

    // @TODO Add the

    // Run each of the installed plugins which implements the 'afterBackup' operation.
    foreach ($this->plugins()->getAllByOp('beforeBackup') as $plugin) {
      $plugin->beforeBackup();
    }

    $file = $source->exportToFile();

    // Run each of the installed plugins which implements the 'afterBackup' operation.
    foreach ($this->plugins()->getAllByOp('afterBackup') as $plugin) {
      $file = $plugin->afterBackup($file);
    }

    // Save the file to the destination.
    $destination->saveFile($file);
  }

  /**
   * {@inheritdoc}
   */
  public function restore($source_id, $destination_id, $file_id = NULL) {
    // Get the source and the destination to use.
    $source = $this->plugins()->get($source_id);
    $destination = $this->plugins()->get($destination_id);

    // @TODO Check the source and destination and throw appropriate exceptions.

    // Load the file from the destination.
    $file = $destination->getFile($file_id);

    // Run each of the installed plugins which implements the 'backup' operation.
    foreach ($this->plugins()->getAllByOp('beforeRestore') as $plugin) {
      $file = $plugin->beforeRestore($file);
    }

    // Do the actual source restore.
    $source->importFromFile($file);
  }

}
