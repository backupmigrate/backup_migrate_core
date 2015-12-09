<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Source\DatabaseSource.
 */

namespace BackupMigrate\Core\Source;

use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Plugin\FileProcessorInterface;
use BackupMigrate\Core\Plugin\FileProcessorTrait;
use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\Translation\TranslatableTrait;

/**
 * Class DatabaseSource
 * @package BackupMigrate\Core\Source
 */
abstract class DatabaseSource  extends PluginBase implements SourceInterface, FileProcessorInterface
{
  use FileProcessorTrait;

  /**
   * Get a definition for user-configurable settings.
   *
   * @param array $params
   * @return array
   */
  public function configSchema($params = array()) {
    $schema = array();
    // @TODO: make this the id of the source.
    $group = 'db';

    // Backup settings.
    if ($params['operation'] == 'backup') {
      $table_select = [
        'group' => $group,
        'type' => 'enum',
        'multiple' => true,
        'options' => $this->getTableNames(),
        'actions' => ['backup']
      ];
      $schema['fields']['exclude_tables'] = $table_select + [
          'title' => $this->t('Exclude these tables entirely'),
        ];

      $schema['fields']['nodata_tables'] = $table_select + [
          'title' => $this->t('Exclude data from these tables'),
        ];
      $schema['groups'][$group] = array(
        // @TODO: Make this the title of the source.
        'title' => 'Database Settings',
      );
    }

    // Init settings.
    else if ($params['operation'] == 'initialize') {
      $schema['fields']['host'] = [
        'type' => 'text',
        'title' => 'Hostname'
      ];
      $schema['fields']['database'] = [
        'type' => 'text',
        'title' => 'Database'
      ];
      $schema['fields']['username'] = [
        'type' => 'text',
        'title' => 'Username',
      ];
      $schema['fields']['password'] = [
        'type' => 'password',
        'title' => 'Password'
      ];
      $schema['fields']['port'] = [
        'type' => 'number',
        'min' => 1,
        'max' => 65535,
        'title' => 'Port',
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
      'generator' => 'Backup and Migrate Core',
    ]);
  }

  /**
   * Get a list of tables in this source
   */
  public function getTableNames() {
    return $this->_getTableNames();
  }

  /**
   * Get an array of tables with some info. Each entry must have at least a
   * 'name' key containing the table name.
   *
   * @return array
   */
  public function getTables() {
    return $this->_getTables();
  }


  /**
   * Get the list of tables from this db.
   *
   * @return array
   */
  protected function _getTableNames() {
    $out = array();
    foreach ($this->_getTables() as $table) {
      $out[$table['name']] = $table['name'];
    }
    return $out;
  }

  /**
   * Internal overridable function to actually generate table info.
   *
   * @return array
   */
  abstract protected function _getTables();
}
