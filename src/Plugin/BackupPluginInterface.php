<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Plugin\BackupPluginInterface.
 */

namespace BackupMigrate\Core\Plugin;

use \BackupMigrate\Core\Config;
use \BackupMigrate\Core\Util\BackupFileInterface;

/**
 * A plugin that runs during the backup process.
 */
interface BackupPluginInterface extends PluginInterface
{
  /**
   * Run on a backup
   *
   * @param \BackupMigrate\Core\Util\BackupFileInterface $file
   * @return \BackupMigrate\Core\Util\BackupFileInterface
   */
  public function backup(BackupFileInterface $file);

  
}
