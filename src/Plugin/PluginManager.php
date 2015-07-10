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
  protected $env;

  /**
   * @param $app
   */
  public function __construct(EnvironmentInterface $env, ConfigInterface $config) {
    $this->env = $env;
    $this->setConfig($config);
    $this->items = array();
  }

  /**
   * Get the app (essentially a dependency injection container for interfacing
   * with the broader app and environment)
   *
   * @return \BackupMigrate\Core\Services\EnvironmentInterface
   */
  public function getEnv() {
    return $this->env;
  }

  /**
   * {@inheritdoc}
   */
  public function add(PluginInterface $item, $id) {
    $this->_preparePlugin($item, $id);
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
    return $this->items;
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
    return $out;
  }

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function call($op, $operand = NULL, $params = array()) {

    // Run each of the installed plugins which implements the given operation.
    foreach ($this->getAllByOp($op) as $plugin) {
      $operand = $plugin->{$op}($operand, $params);
    }

    return $operand;
  }

  /**
   * Prepare the plugin for use. This is called when a plugin is added to the
   * manager and it configures the plugin according to the config object
   * injected into the manager. It also injects other dependencies as needed.
   *
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
      // Don't override plugin config if there is nothing set.
      // This is because sources and destinations are configured before they
      // are passed in to the manager. This maybe something to normalize.
      if ($config !== NULL) {
        $plugin->setConfig(new Config($config));
      }
    }

    // Inject the file processor
    if ($plugin instanceof FileProcessorInterface) {
      $plugin->setTempFileManager($this->getEnv()->getTempFileManager());
    }

    // Inject the plugin manager.
    if ($plugin instanceof PluginCallerInterface) {
      $plugin->setPluginManager($this);
    }

    // @TODO Inject cache/state/logger dependencies
  }
}