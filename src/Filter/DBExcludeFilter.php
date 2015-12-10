<?php
/**
 * @file
 * Contains BackupMigrate\Core\Filter\DBExcludeFilter
 */


namespace BackupMigrate\Core\Filter;


use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Plugin\PluginBase;

/**
 * Allows the exclusion of certain data from a database.
 *
 * Class DBExcludeFilter
 * @package BackupMigrate\Core\Filter
 */
class DBExcludeFilter extends PluginBase {

  /**
   * The 'beforeDBTableBackup' plugin op.
   *
   * @param array $table
   * @param array $params
   * @return array $table
   */
  public function beforeDBTableBackup($table, $params = []) {
    $source = $this->confGet('source');
    if ($source && $source == $params['source']) {
      $exclude = $this->confGet('exclude_tables');
      $nodata = $this->confGet('nodata_tables');
      if (in_array($table['name'], $exclude)) {
        $table['exclude'] = true;
      }
      if (in_array($table['name'], $nodata)) {
        $table['nodata'] = true;
      }
    }
    return $table;
  }

  /**
   * Get the default values for the plugin.
   *
   * @return \BackupMigrate\Core\Config\Config
   */
  public function configDefaults() {
    return new Config([
      'source' => '',
      'exclude_tables' => [],
      'nodata_tables' => [],
    ]);
  }

  /**
   * Get a definition for user-configurable settings.
   *
   * @param array $params
   * @return array
   */
  public function configSchema($params = array()) {
    $schema = array();

    $source = $this->confGet('source');

    // Backup settings.
    if (!empty($source) && $params['operation'] == 'backup') {
      $schema['groups']['default'] = [
        'title' => $this->t('Exclude Data from %source', ['%source' => $source->confGet('name')]),
      ];
      $table_select = [
        'type' => 'enum',
        'multiple' => true,
        'options' => $source->getTableNames(),
        'actions' => ['backup'],
        'group' => 'default'
      ];
      $schema['fields']['exclude_tables'] = $table_select + [
          'title' => $this->t('Exclude these tables entirely'),
        ];

      $schema['fields']['nodata_tables'] = $table_select + [
          'title' => $this->t('Exclude data from these tables'),
        ];
    }
    return $schema;
  }

}