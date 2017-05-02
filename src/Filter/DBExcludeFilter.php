<?php
/**
 * @file
 * Contains BackupMigrate\Core\Filter\DBExcludeFilter
 */


namespace BackupMigrate\Core\Filter;


use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\Plugin\PluginManager;
use BackupMigrate\Core\Source\DatabaseSourceInterface;

/**
 * Allows the exclusion of certain data from a database.
 *
 * Class DBExcludeFilter
 * @package BackupMigrate\Core\Filter
 */
class DBExcludeFilter extends PluginBase {

  /**
   * @var PluginManager
   */
  protected $source_manager;

  /**
   * The 'beforeDBTableBackup' plugin op.
   *
   * @param array $table
   * @param array $params
   * @return array $table
   */
  public function beforeDBTableBackup($table, $params = []) {
    $exclude = $this->confGet('exclude_tables');
    $nodata = $this->confGet('nodata_tables');
    if (in_array($table['name'], $exclude)) {
      $table['exclude'] = true;
    }
    if (in_array($table['name'], $nodata)) {
      $table['nodata'] = true;
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

    if ($params['operation'] == 'backup') {
      $tables = [];

      foreach ($this->sources()->getAll() as $source_key => $source) {
        if ($source instanceof DatabaseSourceInterface) {
          $tables += $source->getTableNames();
        }

        if ($tables) {
          // Backup settings.
          $schema['groups']['default'] = [
            'title' => $this->t('Exclude database tables'),
          ];

          $table_select = [
            'type' => 'enum',
            'multiple' => true,
            'options' => $tables,
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
      }
    }
    return $schema;
  }

  /**
   * @return PluginManager
   */
  public function sources() {
    return $this->source_manager ? $this->source_manager : new PluginManager();
  }

  /**
   * @param PluginManager $source_manager
   */
  public function setSourceManager($source_manager) {
    $this->source_manager = $source_manager;
  }


}
