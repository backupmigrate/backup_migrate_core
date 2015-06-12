<?php
/**
 * @file
 * Contains BackupMigrate\Core\Plugin\PluginManager
 */


namespace BackupMigrate\Core\Plugin;

use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Config\ConfigInterface;
use BackupMigrate\Core\Config\ConfigurableInterface;
use BackupMigrate\Core\Config\ConfigurableTrait;
use BackupMigrate\Core\Services\EnvironmentInterface;

/**
 * Class PluginManager
 * @package BackupMigrate\Core\Plugin
 */
class PluginManager implements PluginManagerInterface, ConfigurableInterface {
  use ConfigurableTrait;

  /**
   * @var \BackupMigrate\Core\Plugin\PluginInterface[]
   */
  protected $items;

  /**
   * @var \BackupMigrate\Core\Services\EnvironmentInterface
   */
  protected $app;

  /**
   * @param $app
   */
  public function __construct(EnvironmentInterface $app, ConfigInterface $config) {
    $this->app = $app;
    $this->setConfig($config);
  }

  /**
   * Get the app (essentially a dependency injection container for interfacing
   * with the broader app and environment)
   *
   * @return \BackupMigrate\Core\Services\EnvironmentInterface
   */
  public function getApp() {
    return $this->app;
  }

  /**
   * {@inheritdoc}
   */
  public function add(PluginInterface $item, $id) {
    $this->items[$id] = $item;
  }

  /**
   * {@inheritdoc}
   **/
  public function get($id) {
    return isset($this->items[$id]) ? $this->items[$id] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getAll() {
    $out = array();
    foreach ((array)$this->items as $id => $plugin) {
      $this->_preparePlugin($plugin, $id);
      $out[] = $plugin;
    }
    return $out;
  }

  /**
   * Get all plugins that implement the given operation.
   *
   * @param string $op The name of the operation.
   * @return \BackupMigrate\Core\Plugin\PluginInterface[]
   */
  public function getAllByOp($op) {
    $out = array();
    $weights = array();

    foreach ($this->getAll() as $plugin) {
      if ($plugin->supportsOp($op)) {
        $out[] = $plugin;
        $weights[] = $plugin->opWeight($op);
      }
    }
    array_multisort($weights, $out);
  }

  /**
   * Get the list of supported file types, optionally for the specified op.
   *
   * @param null $op
   * @return array
   */
  public function supportedFileTypes($op = NULL) {
    $out = array();

    foreach ($this->getAllByOp('getFileTypes') as $plugin) {
      $types = $plugin->getFileTypes();
      foreach ($types as $name => $type) {
        if ($op == NULL || (is_array($type['ops']) && in_array($op, $type['ops']))) {
          $out[$name] = $type;
        }
      }
    }

    return $out;
  }

  /**
   * @param \BackupMigrate\Core\Plugin\PluginInterface $plugin
   *   The plugin to prepare for use.
   * @param string $id
   *   The id of the plugin (to extract the correct settings).
   */
  protected function _preparePlugin($plugin, $id) {
    // If this plugin can be configured, then pass in the configuration.
    if ($plugin instanceof ConfigurableInterface) {
      // Configure the plugin with the appropriate subset of the configuration.
      $config = $this->confGet($id);
      $plugin->setConfig(new Config($config));
    }

    // Inject the file processor
    if ($plugin instanceof FileProcessorInterface) {
      $plugin->setTempFileManager($this->getApp()->getTempFileManager());
    }

    // Inject the plugin manager.
    if ($plugin instanceof PluginCallerInterface) {
      $plugin->setPluginManager($this);
    }

    // @TODO Inject cache/state/logger dependencies
  }

}