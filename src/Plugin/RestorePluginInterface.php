<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Plugin;


use BackupMigrate\Core\Util\ReadableStreamBackupFile;

/**
 * Interface RestorePluginInterface
 * @package BackupMigrate\Core\Plugin
 *
 * A plugin that runs during the backup process.
 */
interface RestorePluginInterface {

  /**
   * Run on restore.
   *
   * @param \BackupMigrate\Core\Util\ReadableStreamBackupFile $file
   *  The file that is being restored from.
   * @return \BackupMigrate\Core\Util\ReadableStreamBackupFile
   */
  public function restore(ReadableStreamBackupFile $file);

}