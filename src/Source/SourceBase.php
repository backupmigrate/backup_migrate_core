<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Source\SourceBase.
 */

namespace BackupMigrate\Core\Source;

use BackupMigrate\Core\Plugin\FileProcessorInterface;
use BackupMigrate\Core\Plugin\FileProcessorTrait;
use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\File\BackupFileReadableInterface;

/**
 * Class SourceBase
 * @package BackupMigrate\Core\Source
 */
abstract class SourceBase extends PluginBase implements SourceInterface, FileProcessorInterface
{
  use FileProcessorTrait;

  /**
   * {@inheritdoc}
   */
  public function supportedOps() {
    return [
      'exportToFile' => [],
      'importFromFile' => []
    ];
  }

  /**
   * Get a definition for user-configurable settings.
   *
   * @param array $params
   * @return array
   */
  public function configSchema($params = array()) {
    $schema = array();

    // Init settings for al destinations
    if ($params['operation'] == 'initialize') {
      $schema['fields']['name'] = [
        'type' => 'text',
        'title' => 'Name'
      ];
    }

    return $schema;
  }


}
