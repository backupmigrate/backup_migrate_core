<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Source\SourceInterface.
 */

namespace BackupMigrate\Core\Source;

use BackupMigrate\Core\Util\BackupFile;

/**
 * Provides an interface defining a backup source.
 */
interface SourceInterface
{

  /**
   * Export this source to the given temp file. This should be the main 
   * back up function for this source.
   * 
   * @param \BackupMigrate\Core\Util\BackupFile $file 
   *    The file to back up to. This file will not yet be opened for writing.
   * 
   * @param array $settings 
   *    An array of settings for this source.
   */
  public function exportToFile(BackupFile $file, $settings);

  /**
   * Import to this source from the given backup file. This is the main restore
   * function for this source.
   * 
   * @param \BackupMigrate\Core\Util\BackupFile $file
   *    The file to read the backup from. It will not be opened for reading
   * 
   * @param array $settings An array of settings
   */
  public function importFromFile(BackupFile $file, $settings);

}
