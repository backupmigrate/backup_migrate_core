<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Source\SourceBase.
 */

namespace BackupMigrate\Core\Source;

use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\Util\BackupFileReadableInterface;

/**
 * Class SourceBase
 * @package BackupMigrate\Core\Source
 */
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
      'exportToFile' => []
    ];
  }

}
