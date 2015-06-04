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
   * @var \BackupMigrate\Core\Plugin\PluginManagerInterface
   */
  protected $plugins;

  /**
   * {@inheritdoc}
   */
  function __construct(ApplicationInterface $app, ConfigInterface $config = NULL) {
    $this->plugins = new PluginManager($app, $config);
  }


  /**
   * {@inheritdoc}
   */
  public function backup($source_id, $destination_id) {
    // Start with an empty file reference, the backup generation plugin will create it for us.
    $file = NULL;
    // Run each of the installed plugins which implements the 'backup' operation.
    foreach ($this->plugins()->getAllByOp('backup') as $plugin) {
      $file = $plugin->backup($file, $filemanager);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function restore($source_id, $destination_id, $file = NULL) {
    // TODO: Implement restore() method.
  }

}
