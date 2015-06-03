<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Plugin\PluginInterface.
 */

namespace BackupMigrate\Core\Plugin;

use \BackupMigrate\Core\Config\ConfigInterface;
//use \BackupMigrate\Core\Services\ApplicationInterface;

/**
 * All of the work is done in plugins. Therefore they may need injected:
 *
 * Sources
 * Destinations
 * Other Plugins?
 * Config
 * Application
 *  Cache
 *  State
 * TempFileManager
 *  TempFileAdapter
 *
 *
 */

/**
 * An interface to describe a Backup and Migrate plugin. Plugins take care
 * of all elements of the backup process and can be configured externally.
 */
interface PluginInterface
{
  /**
   * Set the configuration for this profile.
   * 
   * @param ConfigInterface $config A configuration object containing only configuration for this plugin
   */
  public function setConfig(ConfigInterface $config);

  /**
   * Set the backup and migrate container so that dependencies can be accessed.
   * @TODO this may be an anti-pattern.
   *
   * @param ConfigInterface $config A configuration object containing only configuration for this plugin
   */
  // public function setBackupMigrate(BackupMigrateInterface $bam);

  /**
   * Get a list of supported operations and their weight.
   *
   * @return array
   */
  public function supportedOps();

  /**
   * Plugins are injected with a copy of the plugin manage so that they may call
   * other plugin operations.
   *
   * @param \BackupMigrate\Core\Plugin\PluginManagerInterface $pluginManager
   * @return mixed
   */
  public function setPluginManager(PluginManagerInterface $pluginManager);

  /**
   * @return \BackupMigrate\Core\Plugin\PluginManagerInterface
   */
  public function getPluginManager();

  // Inject a service so that a plugin can get temp files if needed
  // public function setBackupFileManager();

  // Retrieve a schema of some kind to be turned into a configuration form (etc.)
  // public function getConfigSchema();

}
