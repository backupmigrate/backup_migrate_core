<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Plugin\BackupPluginInterface.
 */

namespace BackupMigrate\Core\Plugin;

use \BackupMigrate\Core\Config;
use \BackupMigrate\Core\Util\BackupFileReadableInterface;

/**
 * A plugin that runs during the backup process.
 */
interface BackupPluginInterface extends PluginInterface
{
  /**
   * Run on a backup
   *
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $file
   * @return \BackupMigrate\Core\Util\BackupFileReadableInterface
   */
  public function backup(BackupFileReadableInterface $file);

  
}
