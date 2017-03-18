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
use BackupMigrate\Core\Plugin\PluginCallerInterface;
use BackupMigrate\Core\Plugin\PluginCallerTrait;
use BackupMigrate\Core\Translation\TranslatableTrait;

/**
 * Class MetadataWriter
 * @package BackupMigrate\Core\Filter
 *
 * Add metadata such as a description to the backup file.
 */
class MetadataWriter extends PluginBase implements FileProcessorInterface, PluginCallerInterface {
  use FileProcessorTrait;
  use PluginCallerTrait;

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
      'generator' => 'Backup and Migrate',
      'generatorversion' => defined('BACKUP_MIGRATE_CORE_VERSION') ? constant('BACKUP_MIGRATE_CORE_VERSION') : 'unknown',
      'generatorurl' => 'https://github.com/backupmigrate',
      'bam_sourceid' => '',
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
      'generatorversion',
      'generatorurl',
      'bam_sourceid'
    ];
  }


  /**
   * Run before the backup/restore begins.
   */
  public function setUp($operand, $options) {
    if ($options['operation'] == 'backup' && $options['source_id']) {
      $this->config()->set('bam_sourceid', $options['source_id']);
      if ($source = $this->plugins()->get($options['source_id'])) {
        // @TODO Query the source for it's type and name.
      }
    }
    return $operand;
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
