<?php
/**
 * @file
 * Contains BackupMigrate\Core\Filter\MetadataWriter
 */


namespace BackupMigrate\Core\Filter;


use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\File\BackupFileWritableInterface;
use BackupMigrate\Core\Plugin\FileProcessorInterface;
use BackupMigrate\Core\Plugin\FileProcessorTrait;
use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\Translation\TranslatableTrait;

/**
 * Class MetadataWriter
 * @package BackupMigrate\Core\Filter
 *
 * Add metadata such as a description to the backup file.
 */
class MetadataWriter extends PluginBase implements FileProcessorInterface {
  use FileProcessorTrait;
  use TranslatableTrait;

  /**
   * {@inheritdoc}
   */
  public function configSchema($params = array()) {
    $schema = array();

    // Backup configuration

    if ($params['operation'] == 'backup') {
      $schema['groups']['advanced'] = [
        'title' => 'Advanced Settings',
      ];
      $schema['fields']['description'] = [
        'group' => 'advanced',
        'type' => 'text',
        'title' => 'Description',
        'multiline' => true,
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
      'description' => '',
      'generator' => 'Backup and Migrate (https://github.com/backupmigrate)',
      'generatorversion' => defined('BACKUP_MIGRATE_CORE_VERSION') ? constant('BACKUP_MIGRATE_CORE_VERSION') : 'unknown',
    ]);
  }

  /**
   * Generate a list of metadata keys to be stored with the backup.
   *
   * @return array
   */
  protected function getMetaKeys() {
    return [
      'description',
      'generator',
      'generatorversion'
    ];
  }

  /**
   * Run after a backup. Add metadata to the file.
   *
   * @param \BackupMigrate\Core\File\BackupFileWritableInterface $file
   * @return \BackupMigrate\Core\File\BackupFileWritableInterface
   */
  public function afterBackup(BackupFileWritableInterface $file) {
    // Add the various metadata.
    foreach ($this->getMetaKeys() as $key) {
      $value = $this->confGet($key);
      $file->setMeta($key, $value);
    }
    return $file;
  }

}