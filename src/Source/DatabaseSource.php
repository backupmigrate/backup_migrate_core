<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Source\DatabaseSource.
 */

namespace BackupMigrate\Core\Source;

use BackupMigrate\Core\Plugin\FileProcessorInterface;
use BackupMigrate\Core\Plugin\FileProcessorTrait;
use BackupMigrate\Core\Plugin\PluginBase;

abstract class DatabaseSource  extends PluginBase implements SourceInterface, FileProcessorInterface
{
  use FileProcessorTrait;

  /**
   * Get a definition for user-configurable settings.
   *
   * @return array
   */
  public function configSchema() {
    $schema = array();

    // @TODO: make this the id of the source.
    $group = 'db';

    $table_select = [
      'group' => $group,
      'type' => 'select',
      'multiple' => true,
      'options' => $this->_getTableNames(),
      'actions' => ['backup']
    ];
    $schema['fields']['exclude_tables'] = $table_select + [
      'title' => 'Exclude these tables altogether',
    ];
    $schema['fields']['nodata_tables'] = $table_select + [
      'title' => 'Exclude data from these tables',
    ];

    // Uneditable
    $schema['fields']['generator'] = [
      'default' => 'Backup and Migrate Core',
    ];


    return $form;
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
