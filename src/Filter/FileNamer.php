<?php
/**
 * @file
 * Contains BackupMigrate\Core\Filter\FileNamer
 */


namespace BackupMigrate\Core\Filter;


use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Plugin\FileProcessorInterface;
use BackupMigrate\Core\Plugin\FileProcessorTrait;
use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\File\BackupFileReadableInterface;

/**
 * Class FileNamer
 * @package BackupMigrate\Core\Filter
 */
class FileNamer extends PluginBase implements FileProcessorInterface {
  use FileProcessorTrait;

  /**
   * Get a definition for user-configurable settings.
   *
   * @return array
   */
  public function configSchema() {
    $schema = array();

    $schema['groups']['file'] = [
      'title' => 'Backup File',
    ];
    $schema['fields']['filename'] = [
      'group' => 'file',
      'type' => 'textfield',
      'title' => 'File Name',
      'actions' => ['backup']
    ];
    $schema['fields']['timestamp'] = [
      'group' => 'file',
      'type' => 'checkbox',
      'title' => 'Append a timestamp',
      'actions' => ['backup']
    ];
    $schema['fields']['timestamp_format'] = [
      'group' => 'file',
      'type' => 'textfield',
      'title' => 'Timestamp Format',
      'dependencies' => ['timestamp' => TRUE],
      'actions' => ['backup']
    ];

    return $schema;
  }

  /**
   * Get the default values for the plugin.
   *
   * @return \BackupMigrate\Core\Config\Config
   */
  public function confDefaults() {
    return new Config([
      'filename' => 'backup',
      'timestamp' => TRUE,
      'timestamp_format' => 'Y-m-d\TH-i-s',
    ]);
  }

  /**
   * Get a list of supported operations and their weight.
   *
   * @return array
   */
  public function supportedOps() {
    return [
      'afterBackup' => [],
    ];
  }

  /**
   * Run on a backup. Name the backup file according to the configuration
   *
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $file
   * @return \BackupMigrate\Core\File\BackupFileReadableInterface
   */
  public function afterBackup(BackupFileReadableInterface $file) {
    $name = $this->confGet('filename');
    if ($this->confGet('timestamp')) {
      $name .= '-' . gmdate($this->confGet('timestamp_format'));
    }
    $file->setName($name);
    return $file;
  }

}