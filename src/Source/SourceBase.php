<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Source\SourceBase.
 */

namespace BackupMigrate\Core\Source;

use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\Util\BackupFile;

abstract class SourceBase extends PluginBase implements SourceInterface
{
  /**
   * Get a list of supported operations and their weight.
   *
   * An array of operations should take the form:
   *
   * array(
   *  'backup' => array('weight' => 100),
   *  'restore' => array('weight' => -100),
   * );
   *
   * @return array
   */
  public function supportedOps() {
    return [
      'ManualBackup' => [
        'method' => 'exportToFile',
      ],
      'importFromFile' => []
    ];
  }


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
  abstract public function exportToFile(BackupFile $file);

  /**
   * Import to this source from the given backup file. This is the main restore
   * function for this source.
   *
   * @param \BackupMigrate\Core\Util\BackupFile $file
   *    The file to read the backup from. It will not be opened for reading
   *
   * @param array $settings An array of settings
   */
  abstract public function importFromFile(BackupFile $file);
}
