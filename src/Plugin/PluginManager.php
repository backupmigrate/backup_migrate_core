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
use BackupMigrate\Core\Environment\EnvironmentCallerInterface;
use BackupMigrate\Core\Environment\EnvironmentCallerTrait;
use BackupMigrate\Core\Environment\EnvironmentInterface;
use BackupMigrate\Core\File\TempFileManager;

/**
 * Class PluginManager
 * @package BackupMigrate\Core\Plugin
 */
class PluginManager implements PluginManagerInterface, ConfigurableInterface, EnvironmentCallerInterface {
  use ConfigurableTrait;
  use EnvironmentCallerTrait;

  /**
   * @var \BackupMigrate\Core\Plugin\PluginInterface[]
   */
  protected $items;

  /**
   * @var \BackupMigrate\Core\File\TempFileManagerInterface
   */
  protected $tempFileManager;

  /**
   * @param \BackupMigrate\Core\Environment\EnvironmentInterface $env
   * @param \BackupMigrate\Core\Config\ConfigInterface $config
   */
  public function __construct(EnvironmentInterface $env = NULL, ConfigInterface $config = NULL) {
    // Add the injected environment or a placeholder version.
    $this->setEnvironment($env);

    // Set the configuration or a null object if no config was specified.
    $this->setConfig($config ? $config : new Config());

    // Create an array to store the plugins themselves.
    $this->items = array();

  }


  /**
   * Get the temporary file manager controlled by this plugin manager to be
   * passed as a dependency to plugins. Lazily creates the manager so that
   * a 'blank' plugin manager doesn't take much to initiate.
   *
   * @return \BackupMigrate\Core\File\TempFileManagerInterface
   */
  public function getTempFileManager() {
    // Create a tempFileManager from the environment's temp file adapter.
    if (!$this->tempFileManager) {
      $this->tempFileManager = new TempFileManager(
        $this->env()->getTempFileAdapter()
      );
    }

    return $this->tempFileManager;
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
      $plugin->setTempFileManager($this->getTempFileManager());
    }

    // Inject the plugin manager.
    if ($plugin instanceof PluginCallerInterface) {
      $plugin->setPluginManager($this);
    }

    // Inject the environment dependency container.
    if ($plugin instanceof EnvironmentCallerInterface) {
      $plugin->setEnvironment($this->env());
    }

    // @TODO Inject cache/state/logger/mailer dependencies
    // OR: simply inject the entire environment and let the plugin use what it
    // wants.
  }
}