<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Plugin\BackupPluginInterface.
 */

namespace BackupMigrate\Core\Plugin;

use \BackupMigrate\Core\Config;

/**
 * A plugin that runs during the backup process.
 */
interface BackupPluginInterface extends PluginInterface
{
  /**
   * Run on a backup
   */
  public function backup(BackupFileInterface $file);

  
}
