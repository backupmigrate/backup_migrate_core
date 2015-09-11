<?php
/**
 * @file
 * Contains BackupMigrate\Core\Filter\FileNamer
 */


namespace BackupMigrate\Core\Filter;


use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Config\ValidationError;
use BackupMigrate\Core\Plugin\FileProcessorInterface;
use BackupMigrate\Core\Plugin\FileProcessorTrait;
use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\File\BackupFileReadableInterface;
use BackupMigrate\Core\Translation\TranslatableTrait;

/**
 * Class FileNamer
 * @package BackupMigrate\Core\Filter
 */
class FileNamer extends PluginBase implements FileProcessorInterface {
  use FileProcessorTrait;

  /**
   * {@inheritdoc}
   */
  public function configSchema($params = array()) {
    $schema = array();

    // Backup configuration

    if ($params['operation'] == 'backup') {
      $schema['groups']['file'] = [
        'title' => 'Backup File',
      ];
      $schema['fields']['filename'] = [
        'group' => 'file',
        'type' => 'text',
        'title' => 'File Name',
        'must_match' => '/^[\w\-_]+$/',
        'must_match_error' => $this->t('%title must contain only letters, numbers, dashes (-) and underscores (_).'),
        'min_length' => 1,
        // Allow a 200 character backup name leaving a generous 55 characters
        // for timestamp and extension.
        'max_length' => 200,
        'required' => TRUE,
      ];
      $schema['fields']['timestamp'] = [
        'group' => 'file',
        'type' => 'boolean',
        'title' => 'Append a timestamp',
      ];
      $schema['fields']['timestamp_format'] = [
        'group' => 'file',
        'type' => 'text',
        'title' => 'Timestamp Format',
        'max_length' => 32,
        'dependencies' => ['timestamp' => TRUE],
        'description' => $this->t('Use <a href="http://php.net/date">PHP Date formatting</a>.'),
      ];
    }
    return $schema;
  }

  /**
   * Get the default values for the plugin.
   *
   * @return \BackupMigrate\Core\Config\Config
   */
  public function configDefaults() {
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