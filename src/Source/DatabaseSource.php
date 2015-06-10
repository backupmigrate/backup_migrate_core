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
    $form = array();

    // @TODO: make this the id of the source.
    $group = 'db';

    $form['fields']['exclude_tables'] = [
      'group' => $group,
      'type' => 'select',
      'multiple' => true,
      'title' => 'Exclude these tables altogether',
      'options' => $this->_getTableNames(),
      'actions' => ['backup']
    ];
    $form['fields']['nodata_tables'] = [
      'group' => $group,
      'type' => 'select',
      'multiple' => true,
      'title' => 'Exclude these tables altogether',
      'options' => $this->_getTableNames(),
      'actions' => ['backup']
    ];

    return $form;
  }

  /**
   * Get the list of tables from this db.
   *
   * @return array
   */
  protected function _getTableNames() {
    return [];
  }
}
