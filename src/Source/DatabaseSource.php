<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Source\DatabaseSource.
 */


// Must be injected:
// Database access (PDO object etc.)
//  Takes a set of credentials
//  Allows raw queries
//  Queries return a list of assoc arrays

namespace BackupMigrate\Core\Source;

use BackupMigrate\Core\Util\BackupFile;

abstract class DatabaseSource extends SourceBase
{

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
