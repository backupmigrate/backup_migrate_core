<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Plugin\PluginInterface.
 */

namespace BackupMigrate\Core\Plugin;

use \BackupMigrate\Core\Config;

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

  // Inject a service so that a plugin can get temp files if needed
  // public function setBackupFileManager();

  // Retrieve a schema of some kind to be turned into a configuration form (etc.)
  // public function getConfigSchema();

}
