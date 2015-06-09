<?php
/**
 * @file
 * Contains BackupMigrate\Core\Filter\CreateBackupFilter
 */


namespace BackupMigrate\Core\Filter;


use BackupMigrate\Core\Plugin\BackupPluginInterface;
use BackupMigrate\Core\Plugin\FileProcessorInterface;
use BackupMigrate\Core\Plugin\FileProcessorTrait;
use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\Plugin\PluginInterface;
use BackupMigrate\Core\Services\TempFileManagerInterface;
use BackupMigrate\Core\Util\BackupFileReadableInterface;

/**
 * Class CreateBackupFilter
 * @package BackupMigrate\Core\Filter
 */
class CreateBackupFilter extends PluginBase implements BackupPluginInterface, FileProcessorInterface {
  use FileProcessorTrait;

  /**
   * Run on a backup
   *
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $file
   * @return \BackupMigrate\Core\Util\BackupFileReadableInterface
   */
  public function backup(BackupFileReadableInterface $file) {
    // @TODO Call on the actual source specified to do the backup.
  }

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
      'getFileTypes'    => [],
      'backupSettings'  => [],
      'backup'          => ['weight' => -100],
    ];
  }


}